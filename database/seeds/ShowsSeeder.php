<?php

use Illuminate\Database\Seeder;
use App\Shows;
use App\Movies;
use Carbon\Carbon;

class ShowsSeeder extends Seeder
{
    const MINIMUM_GAP = 20;

    public function run()
    {
        // 1. Fetch the specific movies so we have their exact durations
        $movieIds = [25, 18, 26, 27];
        $movies = Movies::whereIn('movie_id', $movieIds)->get()->keyBy('movie_id');

        if ($movies->count() < 4) {
            $this->command->error('Missing one or more movies in the database. Please ensure movies 25, 18, 26, and 27 exist.');
            return;
        }

        // 2. Set up the slots. (1140 minutes = 7:00 PM. Everything before that is Daytime)
        $selectedSlots = [
            ['anchor_minutes' => 600,  'movie' => $movies[25]], // 10:00 AM
            ['anchor_minutes' => 780,  'movie' => $movies[18]], // 1:00 PM
            ['anchor_minutes' => 960,  'movie' => $movies[26]], // 4:00 PM
            ['anchor_minutes' => 1140, 'movie' => $movies[27]], // 7:00 PM (Evening Lock)
        ];

        // 3. Generate the daily schedule using the exact same logic from the controller
        $dailySchedule = $this->generateDailySchedule($selectedSlots);

        // 4. Set timeframe: 5 days from now to 10 days from now
        $startDate = Carbon::today()->addDays(5);
        $endDate = Carbon::today()->addDays(10);

        // 5. Loop through dates and insert the calculated shows
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');

            foreach ($dailySchedule as $slot) {
                $startMins = $slot['start'];
                
                // Convert integer minutes (e.g., 600) back to time string (e.g., "10:00:00")
                $timeString = sprintf('%02d:%02d:00', intdiv($startMins, 60), $startMins % 60);

                // Use firstOrCreate to protect your existing past data
                Shows::firstOrCreate(
                    [
                        'date' => $formattedDate, 
                        'time' => $timeString
                    ],
                    [
                        'movies_movie_id' => $slot['movie']->movie_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
        
        $this->command->info('Shows scheduled successfully using the dynamic timing logic.');
    }

    // --- Helper function copied from the controller to ensure perfect sync ---
    private function generateDailySchedule($selectedSlots)
    {
        $schedule = [];
        $daytimeSlots = [];
        $eveningSlots = [];

        foreach ($selectedSlots as $slot) {
            if ($slot['anchor_minutes'] < 1140) {
                $daytimeSlots[] = $slot;
            } else {
                $eveningSlots[] = $slot;
            }
        }

        // --- 1. Process Daytime Shows (10:00 AM to 7:00 PM) ---
        if (count($daytimeSlots) > 0) {
            $cursor = $daytimeSlots[0]['anchor_minutes'];
            $eveningAnchor = 1140; // Hard limit: 7:00 PM
            
            $totalDuration = 0;
            foreach ($daytimeSlots as $slot) {
                $totalDuration += $slot['movie']->duration + self::MINIMUM_GAP;
            }

            $freeTime = max(0, $eveningAnchor - $cursor - $totalDuration);
            $freeShare = (int) floor($freeTime / count($daytimeSlots));

            foreach ($daytimeSlots as $slot) {
                $movie = $slot['movie'];
                $start = $cursor;
                $end = $start + $movie->duration;
                $schedule[] = ['movie' => $movie, 'start' => $start, 'end' => $end];

                // Apply free time share and round DOWN to nearest 15 mins (previous quarter)
                $cursor = (int) (floor(($end + self::MINIMUM_GAP + $freeShare) / 15) * 15);
            }
        }

        // --- 2. Process Evening Shows (7:00 PM onwards) ---
        if (count($eveningSlots) > 0) {
            $cursor = max(empty($schedule) ? 0 : end($schedule)['end'] + self::MINIMUM_GAP, $eveningSlots[0]['anchor_minutes']);
            
            foreach ($eveningSlots as $slot) {
                $movie = $slot['movie'];
                $start = $cursor;
                $end = $start + $movie->duration;
                $schedule[] = ['movie' => $movie, 'start' => $start, 'end' => $end];

                // Evening shows minimum gap, rounded UP to nearest 15 mins (next quarter)
                $cursor = (int) (ceil(($end + self::MINIMUM_GAP) / 15) * 15);
            }
        }

        return $schedule;
    }
}