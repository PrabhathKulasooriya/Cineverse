<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Shows;
use App\Movies;
use App\Showtimes;
use App\Bookings;
use DB;
use Carbon\Carbon;

class ShowsController extends Controller
{
    const MINIMUM_GAP = 20;
    const BUSINESS_END = 1470; // 24*60 + 30 = 12:30 AM

    public function index()
    {
        $enabledSlotsCount = Showtimes::where('status', 1)->count();
        $showsPerDate = Shows::whereDate('date', '>=', now()->toDateString())
            ->selectRaw('date, count(show_id) as count')
            ->groupBy('date')
            ->get();
        $fullyBookedDates = $showsPerDate->where('count', '>=', $enabledSlotsCount)->pluck('date')->toArray();

        $shows = Shows::whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?", [now()->subHours(2)])->get();
        $movies = Movies::where('status', 1)->where('screening_status', 1)->get();
        $showtimes = Showtimes::orderBy('time', 'asc')->get();
        $enabledShowtimes = $showtimes->where('status', 1)->values();
        $bookedShowIds = DB::table('bookings')->pluck('shows_show_id')->unique()->toArray();

        $shows->map(function ($show) use ($movies, $bookedShowIds) {
            $show->movie_name = $movies->where('movie_id', $show->movies_movie_id)->first()->name ?? 'No movie found';
            $show->has_bookings = in_array($show->show_id, $bookedShowIds);
            return $show;
        });

        return view('movies.shows', compact('shows', 'movies', 'showtimes', 'enabledShowtimes', 'fullyBookedDates'), ['title' => 'Manage Upcoming Shows']);
    }

    public function screened()
    {
        $shows = Shows::whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') < ?", [now()])->get();
        $movies = Movies::all();
        $showtimes = Showtimes::all();

        $shows->map(function ($show) use ($movies) {
            $show->movie_name = $movies->where('movie_id', $show->movies_movie_id)->first()->name ?? 'No movie found';
            return $show;
        });

        return view('movies.screenedShows', compact('shows', 'movies', 'showtimes'), ['title' => 'Screened Shows']);
    }

    public function storeShows(Request $request)
    {
        $enabledShowtimes = Showtimes::where('status', 1)->orderBy('time', 'asc')->get();
        if ($enabledShowtimes->isEmpty()) return response()->json(['errors' => 'No enabled showtimes. Please enable showtimes first.']);

        $rules = ['start_date' => 'required|date', 'end_date' => 'nullable|date|after_or_equal:start_date'];
        $messages = [
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be on or after start date.'
        ];

        foreach ($enabledShowtimes as $index => $st) {
            $slotNum = $index + 1;
            $formattedTime = Carbon::parse($st->time)->format('h:i A');
            $rules['movie' . $slotNum] = 'required|exists:movies,movie_id';
            $messages['movie' . $slotNum . '.required'] = "Please select a movie for Slot {$slotNum} ({$formattedTime}).";
            $messages['movie' . $slotNum . '.exists'] = "Selected movie for Slot {$slotNum} is invalid.";
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) return response()->json(['errors' => $validator->errors()]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate->copy();

        if ($endDate->gt(Carbon::today()->addMonths(2))) {
            return response()->json(['errors' => 'Shows cannot be created more than 2 months in advance.']);
        }

        $selectedSlots = [];
        foreach ($enabledShowtimes as $index => $showtime) {
            $movieId = $request->input('movie' . ($index + 1));
            $movie = Movies::find($movieId);
            if (!$movie) {
                return response()->json(['errors' => "Selected movie for Slot " . ($index + 1) . " not found."]);
            }
            $selectedSlots[] = [
                'anchor_minutes' => $this->convertTimeToMinutes($showtime->time),
                'movie' => $movie
            ];
        }

        $dailyScheduleResult = $this->generateDailySchedule($selectedSlots);
        if (isset($dailyScheduleResult['error'])) {
            return response()->json(['errors' => $dailyScheduleResult['error']]);
        }
        $dailySchedule = $dailyScheduleResult['schedule'];

        $showsToCreate = [];
        $bookedRangesByDate = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();
            $formattedDate = $date->format('d-m-Y');

            // 1. Check movie release date for each show on this date
            foreach ($dailySchedule as $slot) {
                $movie = $slot['movie'];
                if ($movie->release_date && Carbon::parse($dateString)->lt(Carbon::parse($movie->release_date))) {
                    $relDate = Carbon::parse($movie->release_date)->format('d-m-Y');
                    return response()->json([
                        'errors' => "Cannot create schedule: Movie '{$movie->name}' is scheduled before its release date ({$relDate}) on {$formattedDate}. No shows were created."
                    ]);
                }
            }

            // 2. Check for time conflicts with existing shows in DB
            if (!isset($bookedRangesByDate[$dateString])) {
                $bookedRangesByDate[$dateString] = $this->loadBookedRanges($dateString);
            }
            $bookedRanges = &$bookedRangesByDate[$dateString];

            foreach ($dailySchedule as $slot) {
                $targetDate = $dateString;
                $startMins = $slot['start'];
                $endMins = $slot['end'];

                if ($startMins >= 1440) {
                    $targetDate = Carbon::parse($dateString)->addDay()->toDateString();
                    $startMins -= 1440;
                    $endMins -= 1440;
                    if (!isset($bookedRangesByDate[$targetDate])) {
                        $bookedRangesByDate[$targetDate] = $this->loadBookedRanges($targetDate);
                    }
                    $checkBooked = &$bookedRangesByDate[$targetDate];
                } else {
                    $checkBooked = &$bookedRanges;
                }

                if ($this->hasConflict($startMins, $endMins, $checkBooked, self::MINIMUM_GAP)) {
                    $formattedTimeAmPm = Carbon::parse(sprintf('%02d:%02d:00', intdiv($startMins, 60), $startMins % 60))->format('h:i A');
                    $formattedTargetDate = Carbon::parse($targetDate)->format('d-m-Y');
                    return response()->json([
                        'errors' => "Time conflict on {$formattedTargetDate} at {$formattedTimeAmPm} for movie '{$slot['movie']->name}' with an existing show. No shows were created."
                    ]);
                }

                $showsToCreate[] = [
                    'movies_movie_id' => $slot['movie']->movie_id,
                    'date' => $targetDate,
                    'time' => sprintf('%02d:%02d:00', intdiv($startMins, 60), $startMins % 60),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                $checkBooked[] = ['start' => $startMins, 'end' => $endMins];
            }
        }

        if (empty($showsToCreate)) {
            return response()->json(['errors' => 'No shows could be created.']);
        }

        Shows::insert($showsToCreate); // Batch insert for speed

        return response()->json(['success' => count($showsToCreate) . ' show(s) created successfully.']);
    }

    private function generateDailySchedule($selectedSlots)
    {
        $schedule = [];
        if (empty($selectedSlots)) return ['schedule' => []];

        $daytimeSlots = [];
        $eveningSlots = [];

        // 1140 minutes is 7:00 PM
        foreach ($selectedSlots as $slot) {
            if ($slot['anchor_minutes'] < 1140) {
                $daytimeSlots[] = $slot;
            } else {
                $eveningSlots[] = $slot;
            }
        }

        // --- 1. Process Daytime Shows (Anchored at 10:00 AM up to 7:00 PM) ---
        if (count($daytimeSlots) > 0) {
            $cursor = $daytimeSlots[0]['anchor_minutes']; // 10:00 AM anchor (600)
            $eveningAnchor = 1140; // Hard limit: 7:00 PM
            
            $totalDuration = 0;
            foreach ($daytimeSlots as $slot) {
                $totalDuration += $slot['movie']->duration + self::MINIMUM_GAP;
            }

            $freeTime = $eveningAnchor - $cursor - $totalDuration;
            if ($freeTime < 0) {
                return ['error' => 'Daytime schedule runs past 7:00 PM anchor. Pick shorter movies.'];
            }

            $freeShare = (int) floor($freeTime / count($daytimeSlots));

            foreach ($daytimeSlots as $slot) {
                $movie = $slot['movie'];
                $start = $cursor;
                $end = $start + $movie->duration;
                $schedule[] = ['movie' => $movie, 'start' => $start, 'end' => $end];

                // Apply free time share and round DOWN to nearest 15 mins (previous quarter)
                $cursor = (int) (floor(($end + self::MINIMUM_GAP + $freeShare) / 15) * 15);
            }

            if (!empty($schedule) && end($schedule)['end'] > $eveningAnchor) {
                return ['error' => 'Daytime schedule runs past 7:00 PM anchor. Pick shorter movies.'];
            }
        }

        // --- 2. Process Evening Shows (Anchored at 7:00 PM onwards) ---
        if (count($eveningSlots) > 0) {
            // Anchor 7:00 PM show at 7:00 PM (1140)
            $cursor = $eveningSlots[0]['anchor_minutes']; // 1140 (7:00 PM)
            
            foreach ($eveningSlots as $slot) {
                $movie = $slot['movie'];
                $start = $cursor;
                $end = $start + $movie->duration;
                $schedule[] = ['movie' => $movie, 'start' => $start, 'end' => $end];

                // Evening shows minimum gap, rounded UP to nearest 15 mins (next quarter)
                $cursor = (int) (ceil(($end + self::MINIMUM_GAP) / 15) * 15);
            }

            if (!empty($schedule) && end($schedule)['end'] > self::BUSINESS_END) {
                return ['error' => 'Evening lineup runs past 12:30 AM. Pick shorter movies.'];
            }
        }

        return ['schedule' => $schedule];
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hiddenMovieID' => 'required',
            'movie' => 'required|exists:movies,movie_id',
            'date' => 'required|date',
            'time' => 'required',
        ]);
        if ($validator->fails()) return response()->json(['errors' => $validator->errors()]);

        $show = Shows::find($request->hiddenMovieID);
        if (!$show) return response()->json(['errors' => 'Show not found.']);
        if (Bookings::where('shows_show_id', $show->show_id)->count() > 0) return response()->json(['errors' => 'This show has bookings and cannot be edited.']);

        $movie = Movies::find($request->movie);
        if (!$movie) return response()->json(['errors' => 'Selected movie not found.']);

        if ($movie->release_date && Carbon::parse($request->date)->lt(Carbon::parse($movie->release_date))) {
            $relDate = Carbon::parse($movie->release_date)->format('d-m-Y');
            $showDate = Carbon::parse($request->date)->format('d-m-Y');
            return response()->json(['errors' => "Cannot update show: Movie '{$movie->name}' is scheduled before its release date ({$relDate}) on {$showDate}."]);
        }

        $startMins = $this->convertTimeToMinutes($request->time);
        
        $bookedRanges = $this->loadBookedRanges($request->date, $show->show_id);
        if ($this->hasConflict($startMins, $startMins + $movie->duration, $bookedRanges, self::MINIMUM_GAP)) {
            return response()->json(['errors' => 'Time conflict with another show.']);
        }

        $show->update(['movies_movie_id' => $request->movie, 'date' => $request->date, 'time' => $request->time]);
        return response()->json(['success' => 'Show updated successfully.']);
    }

    public function destroy(Request $request)
    {
        $show = Shows::find($request->show_id);
        if (!$show) return response()->json(['errors' => 'Not found.']);
        if (Bookings::where('shows_show_id', $show->show_id)->exists()) return response()->json(['errors' => 'Has bookings.']);
        
        $show->delete();
        return response()->json(['success' => 'Deleted successfully.']);
    }

    public function getAvailableShowtimes(Request $request)
    {
        $date = $request->date;
        $movieId = $request->movie_id;
        $excludeShowId = $request->exclude_show_id;
        
        $bookedRanges = $this->loadBookedRanges($date, $excludeShowId);
        $movieDuration = $movieId ? Movies::find($movieId)->duration : null;

        $available = [];
        foreach (Showtimes::where('status', 1)->orderBy('time', 'asc')->get() as $showtime) {
            $start = $this->convertTimeToMinutes($showtime->time);
            $end = $start + ($movieDuration ?? self::MINIMUM_GAP);
            
            if ($movieDuration !== null && $this->hasConflict($start, $end, $bookedRanges, self::MINIMUM_GAP)) continue;
            if ($movieDuration === null && in_array($start, array_column($bookedRanges, 'start'))) continue;

            $showtime->formatted_time = Carbon::parse($showtime->time)->format('h:i A');
            $available[] = $showtime;
        }

        return response()->json(['success' => true, 'showtimes' => $available]);
    }

    // --- Helpers ---
   private function loadBookedRanges($date, $excludeId = null)
    {
        return DB::table('shows')
            ->join('movies', 'movies.movie_id', '=', 'shows.movies_movie_id')
            ->where('shows.date', $date)
            ->when($excludeId, function($q) use ($excludeId) {
                return $q->where('shows.show_id', '!=', $excludeId);
            })
            ->get()
            ->map(function ($show) {
                $start = $this->convertTimeToMinutes($show->time);
                return ['start' => $start, 'end' => $start + $show->duration];
            })->toArray();
    }

    private function convertTimeToMinutes($time)
    {
        $parts = explode(':', $time);
        return ((int)$parts[0] * 60) + (int)$parts[1];
    }

    private function hasConflict($start, $end, $bookedRanges, $minGap)
    {
        foreach ($bookedRanges as $range) {
            if ($start < ($range['end'] + $minGap) && ($end + $minGap) > $range['start']) return true;
        }
        return false;
    }
}