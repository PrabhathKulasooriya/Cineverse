<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bookings;
use App\User;
use App\BookedSeats;
use App\Seats;
use App\Movies;
use App\Shows;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    // Client Report*************************************************************************************
    public function clientReport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        
        $reportData = null;
        
        if ($startDate && $endDate) {
            // Validate dates
            try {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Invalid date format');
            }
            
            $reportData = $this->generateClientReport($startDate, $endDate);
        } else {
            
            $reportData = $this->generateClientReport(null, null);
        }
        
        return view('reports.clientReport', ['title' => 'Client Report'], compact('reportData'));
    }
    
    private function generateClientReport($startDate, $endDate)
    {
        // Build query for registered users data
        $registeredUsersQuery = Bookings::with('user')
            ->join('master_user', 'bookings.master_user_idmaster_user', '=', 'master_user.idmaster_user')
            ->whereNotNull('bookings.master_user_idmaster_user')
            ->where('master_user.user_role_iduser_role', 4); 
        
        // Apply date filter only if dates are provided
        if ($startDate && $endDate) {
            $registeredUsersQuery->whereBetween('bookings.created_at', [$startDate, $endDate]);
        }
        
        $registeredUsersData = $registeredUsersQuery
            ->select(
                'master_user.idmaster_user as user_id',
                'master_user.first_name',
                'master_user.last_name',
                'master_user.email',
                DB::raw('COUNT(bookings.booking_id) as bookings'),
                DB::raw('SUM(bookings.amount) as total_spent'),
                DB::raw('CONCAT(master_user.first_name, " ", master_user.last_name) as full_name')
            )
            ->groupBy('master_user.idmaster_user', 'master_user.first_name', 'master_user.last_name', 'master_user.email')
            ->orderBy('total_spent', 'desc')
            ->get();
        
        // Build query for guest bookings and revenue
        $guestQuery = DB::table('bookings')
            ->whereNull('master_user_idmaster_user'); 
        
        
        if ($startDate && $endDate) {
            $guestQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $guestData = $guestQuery
            ->select(
                DB::raw('COUNT(booking_id) as guest_bookings'),
                DB::raw('SUM(amount) as guest_revenue')
            )
            ->first();
        
        // Calculate totals
        $registeredUsersTotal = [
            'bookings' => $registeredUsersData->sum('bookings'),
            'revenue' => $registeredUsersData->sum('total_spent')
        ];
        
        $guestBookings = $guestData->guest_bookings ?? 0;
        $guestRevenue = $guestData->guest_revenue ?? 0;
        
        $totalRevenue = $registeredUsersTotal['revenue'] + $guestRevenue;
        
        // Prepare top spenders and lowest spender
        $topSpenders = $registeredUsersData->take(3)->map(function($user) {
            return [
                'name' => $user->full_name,
                'total_spent' => $user->total_spent
            ];
        })->toArray();
        
        $lowestSpender = $registeredUsersData->count() > 0 ? 
            $registeredUsersData->sortBy('total_spent')->first() : null;
        
        
        $usersFormatted = $registeredUsersData->map(function($user) {
            return [
                'user_id' => 'REG-' . str_pad($user->user_id, 3, '0', STR_PAD_LEFT),
                'name' => $user->full_name,
                'email' => $user->email,
                'bookings' => $user->bookings,
                'total_spent' => $user->total_spent
            ];
        })->toArray();
        
        return [
            'users' => $usersFormatted,
            'guest_bookings' => $guestBookings,
            'guest_revenue' => $guestRevenue,
            'registered_users_total' => $registeredUsersTotal,
            'total_revenue' => $totalRevenue,
            'total_users' => $registeredUsersData->count(),
            'top_spenders' => $topSpenders,
            'lowest_spender' => $lowestSpender ? [
                'name' => $lowestSpender->full_name,
                'total_spent' => $lowestSpender->total_spent
            ] : null
        ];
    }

    //Revenue Report*******************************************************************************
    public function revenueReport(Request $request)
{
    $startDate = $request->input('startDate');
    $endDate = $request->input('endDate');
    
    $reportData = null;
    
    if ($startDate && $endDate) {
        // Validate dates
        try {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid date format');
        }
        
        $reportData = $this->generateRevenueReport($startDate, $endDate);
    } else {
        $reportData = $this->generateRevenueReport(null, null);
    }
    
    // Prepare data for the view
    $viewData = $this->prepareRevenueReportView($reportData);
    
    return view('reports.revenueReport', $viewData);
}

private function generateRevenueReport($startDate, $endDate)
{
    // Get shows with date filter
    $showsQuery = Shows::with(['movies']);
    
    // Apply date filter if provided
    if ($startDate && $endDate) {
        $showsQuery->whereBetween('date', [$startDate, $endDate]);
    }
    
    $shows = $showsQuery->orderBy('date', 'desc')
                       ->orderBy('time', 'desc')
                       ->get();
    
    // Process each show to calculate revenue and seat counts
    $showsData = [];
    $totalRevenue = 0;
    $totalSeatsBooked = 0;
    $totalPrimeSeatsBooked = 0;
    $totalNormalSeatsBooked = 0;
    
    foreach ($shows as $show) {
        $primeSeats = 0;
        $normalSeats = 0;
        $showRevenue = 0;
        
        // Get bookings for this show
        $bookings = Bookings::with(['bookedSeats.seat'])
            ->where('shows_show_id', $show->show_id)
            ->get();
        
        // Calculate seats and revenue for this show
        foreach ($bookings as $booking) {
            $showRevenue += $booking->amount;
            
            foreach ($booking->bookedSeats as $bookedSeat) {
                if ($bookedSeat->seat) {
                    if ($bookedSeat->seat->seat_type_idseat_type == 2) {
                        $primeSeats++;
                    } elseif ($bookedSeat->seat->seat_type_idseat_type == 1) {
                        $normalSeats++;
                    }
                }
            }
        }
        
        $totalSeatsForShow = $primeSeats + $normalSeats;
        
        $showsData[] = (object) [
            'show_id' => $show->show_id,
            'show_date' => $show->date,
            'show_time' => $show->time,
            'movie_name' => $show->movies ? $show->movies->name : 'Unknown Movie',
            'prime_seats_booked' => $primeSeats,
            'normal_seats_booked' => $normalSeats,
            'total_seats_booked' => $totalSeatsForShow,
            'total_revenue' => $showRevenue
        ];
        
        // Add to totals
        $totalRevenue += $showRevenue;
        $totalSeatsBooked += $totalSeatsForShow;
        $totalPrimeSeatsBooked += $primeSeats;
        $totalNormalSeatsBooked += $normalSeats;
    }
    
    // Get seat configuration (total seats available)
    $primeSeatsTotal = Seats::where('seat_type_idseat_type', 2)->count();
    $normalSeatsTotal = Seats::where('seat_type_idseat_type', 1)->count();
    $totalSeatsPerHall = $primeSeatsTotal + $normalSeatsTotal;
    
    // Calculate pricing information from recent bookings
    $pricingData = $this->calculateSeatPricing();
    
    // Calculate summary statistics
    $totalShows = count($showsData);
    $averageRevenuePerShow = $totalShows > 0 ? $totalRevenue / $totalShows : 0;
    
    // Calculate hall capacity utilization
    $totalCapacity = $totalShows * $totalSeatsPerHall;
    $utilizationRate = $totalCapacity > 0 ? ($totalSeatsBooked / $totalCapacity) * 100 : 0;
    
    return [
        'shows_data' => collect($showsData),
        'seat_counts' => [
            'prime_seats' => $primeSeatsTotal,
            'normal_seats' => $normalSeatsTotal,
            'total_seats' => $totalSeatsPerHall
        ],
        'pricing_data' => $pricingData,
        'summary' => [
            'total_shows' => $totalShows,
            'total_revenue' => $totalRevenue,
            'average_revenue_per_show' => $averageRevenuePerShow,
            'total_seats_booked' => $totalSeatsBooked,
            'prime_seats_booked' => $totalPrimeSeatsBooked,
            'normal_seats_booked' => $totalNormalSeatsBooked,
            'hall_capacity_utilization' => $utilizationRate
        ]
    ];
}

private function calculateSeatPricing()
{
    // Get recent bookings to calculate average pricing per seat type
    $recentBookings = Bookings::with(['bookedSeats.seat'])
        ->whereHas('bookedSeats.seat')
        ->orderBy('created_at', 'desc')
        ->limit(100) // Get recent 100 bookings for pricing calculation
        ->get();
    
    $primeTotal = 0;
    $primeCount = 0;
    $normalTotal = 0;
    $normalCount = 0;
    
    foreach ($recentBookings as $booking) {
        $seatsInBooking = $booking->bookedSeats->count();
        $pricePerSeat = $seatsInBooking > 0 ? $booking->amount / $seatsInBooking : 0;
        
        foreach ($booking->bookedSeats as $bookedSeat) {
            if ($bookedSeat->seat) {
                if ($bookedSeat->seat->seat_type_idseat_type == 2) {
                    $primeTotal += $pricePerSeat;
                    $primeCount++;
                } elseif ($bookedSeat->seat->seat_type_idseat_type == 1) {
                    $normalTotal += $pricePerSeat;
                    $normalCount++;
                }
            }
        }
    }
    
    return [
        'prime_avg_price' => $primeCount > 0 ? $primeTotal / $primeCount : 1750,
        'normal_avg_price' => $normalCount > 0 ? $normalTotal / $normalCount : 1300
    ];
}

private function prepareRevenueReportView($reportData)
{
    if (!$reportData || $reportData['shows_data']->isEmpty()) {
        return [
            'reportData' => null,
            'noDataText' => 'No revenue data found for the selected date range.'
        ];
    }
    
    
    $tableData = [];
    foreach ($reportData['shows_data'] as $show) {
        $showDateTime = Carbon::parse($show->show_date . ' ' . $show->show_time)->format('Y-m-d H:i A');
        $primeSeats = $show->prime_seats_booked . '/' . $reportData['seat_counts']['prime_seats'];
        $normalSeats = $show->normal_seats_booked . '/' . $reportData['seat_counts']['normal_seats'];
        
        $tableData[] = [
            'show_id' => 'SH-' . str_pad($show->show_id, 3, '0', STR_PAD_LEFT),
            'movie_name' => strtoupper($show->movie_name),
            'datetime' => $showDateTime,
            'prime_seats' => $primeSeats,
            'normal_seats' => $normalSeats,
            'total_revenue' => [
                'value' => number_format($show->total_revenue, 2),
                'class' => 'text-success font-weight-bold'
            ]
        ];
    }
    
    // Get pricing information
    $primeSeatPrice = $reportData['pricing_data']['prime_avg_price'];
    $normalSeatPrice = $reportData['pricing_data']['normal_avg_price'];
    
    // Additional Cards for seat pricing and summary
    $additionalCards = [
        [
            'title' => 'Seat Pricing Information',
            'col_size' => 6,
            'content' => [
                ['label' => 'Prime Seats', 'value' => 'LKR ' . number_format($primeSeatPrice) . ' each (' . $reportData['seat_counts']['prime_seats'] . ' seats)'],
                ['label' => 'Normal Seats', 'value' => 'LKR ' . number_format($normalSeatPrice) . ' each (' . $reportData['seat_counts']['normal_seats'] . ' seat.)']
            ]
        ],
        [
            'title' => 'Summary Statistics',
            'col_size' => 6,
            'content' => [
                ['label' => 'Total Shows', 'value' => $reportData['summary']['total_shows']],
                ['label' => 'Average Revenue per Show', 'value' => 'LKR ' . number_format($reportData['summary']['average_revenue_per_show'], 2)],
                ['label' => 'Hall Capacity Utilization', 'value' => number_format($reportData['summary']['hall_capacity_utilization'], 1) . '%']
            ]
        ]
    ];
    
    // Alert Summary - This matches what the blade template expects
    $alertSummary = [
        'title' => 'Revenue Summary',
        'icon' => 'fas fa-chart-line',
        'items' => [
            ['label' => 'Total Shows', 'value' => $reportData['summary']['total_shows'], 'col_size' => 3],
            ['label' => 'Total Revenue', 'value' => 'LKR ' . number_format($reportData['summary']['total_revenue'], 2), 'col_size' => 3],
            ['label' => 'Prime Seats Sold', 'value' => $reportData['summary']['prime_seats_booked'], 'col_size' => 3],
            ['label' => 'Normal Seats Sold', 'value' => $reportData['summary']['normal_seats_booked'], 'col_size' => 3]
        ]
    ];
    
    return [
        'title' => 'Revenue Report',
        'reportData' => $reportData,
        'reportTitle' => 'Revenue Report',
        'alertSummary' => $alertSummary,
        'tableData' => $tableData,
        'additionalCards' => $additionalCards,
        'noDataMessage' => 'No revenue data found for the selected date range.',
        'noDataText' => 'Please select a date range and click "Search" to generate the revenue report.'
    ];
}
}