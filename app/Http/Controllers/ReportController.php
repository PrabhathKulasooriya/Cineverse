<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bookings;
use App\Shows;
use App\Movies;
use App\Payments;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Monthly Revenue Report *****************************************************************
    public function monthlyRevenueReport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        if (!$startDate || !$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
        }

        $payments = Payments::with('booking')
            ->whereHas('booking', function ($query) {
                $query->where('payment_status', 'PAID');
            })
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->get();

        $groupedByMonth = $payments->groupBy(function ($payment) {
            return Carbon::parse($payment->created_at)->format('Y-m');
        })->sortKeys();

        $chartLabels = [];
        $chartSeatData = [];
        $chartSnackData = [];
        $chartTotalData = [];
        $tableRows = [];
        $grandSeatTotal = 0;
        $grandSnackTotal = 0;

        foreach ($groupedByMonth as $month => $monthPayments) {
            $seatRevenue = 0;
            $totalRevenue = 0;

            foreach ($monthPayments as $payment) {
                if ($payment->booking) {
                    $seatRevenue += $payment->booking->amount;
                }
                $totalRevenue += $payment->amount;
            }

            $snackRevenue = $totalRevenue - $seatRevenue;

            $chartLabels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $chartSeatData[] = round($seatRevenue, 2);
            $chartSnackData[] = round($snackRevenue, 2);
            $chartTotalData[] = round($totalRevenue, 2);

            $tableRows[] = [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'seat_revenue' => $seatRevenue,
                'snack_revenue' => $snackRevenue,
                'total_revenue' => $totalRevenue
            ];

            $grandSeatTotal += $seatRevenue;
            $grandSnackTotal += $snackRevenue;
        }

        return view('reports.monthlyRevenueReport', [
            'title' => 'Monthly Revenue Report',
            'tableRows' => $tableRows,
            'chartLabels' => $chartLabels,
            'chartSeatData' => $chartSeatData,
            'chartSnackData' => $chartSnackData,
            'chartTotalData' => $chartTotalData,
            'grandSeatTotal' => $grandSeatTotal,
            'grandSnackTotal' => $grandSnackTotal,
            'grandTotal' => $grandSeatTotal + $grandSnackTotal
        ]);
    }

    // Movie Ticket Income Report *************************************************************
    public function movieIncomeReport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $bookingsQuery = Bookings::with(['movie', 'bookedSeats'])
            ->where('payment_status', 'PAID');

        if ($startDate && $endDate) {
            $bookingsQuery->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        }

        $bookings = $bookingsQuery->get();
        $groupedByMovie = $bookings->groupBy('movies_movie_id');

        $movieIncomeData = [];
        foreach ($groupedByMovie as $movieId => $movieBookings) {
            $totalTickets = 0;
            $totalIncome = 0;

            foreach ($movieBookings as $booking) {
                $totalTickets += $booking->bookedSeats->count();
                $totalIncome += $booking->amount;
            }

            $firstBooking = $movieBookings->first();

            $movieIncomeData[] = (object) [
                'movie_id' => $movieId,
                'movie_name' => $firstBooking->movie ? $firstBooking->movie->name : 'Unknown Movie',
                'total_bookings' => $movieBookings->count(),
                'total_tickets' => $totalTickets,
                'total_income' => $totalIncome
            ];
        }

        usort($movieIncomeData, function ($a, $b) {
            return $b->total_income <=> $a->total_income;
        });

        $activeMovieIds = Shows::where('date', '>=', Carbon::now()->format('Y-m-d'))
            ->pluck('movies_movie_id')
            ->unique()
            ->toArray();

        $topActiveMovies = [];
        foreach ($movieIncomeData as $movie) {
            if (in_array($movie->movie_id, $activeMovieIds)) {
                $topActiveMovies[] = $movie;
            }
            if (count($topActiveMovies) == 5) {
                break;
            }
        }

        $chartLabels = [];
        $chartData = [];
        foreach ($topActiveMovies as $movie) {
            $chartLabels[] = $movie->movie_name;
            $chartData[] = round($movie->total_income, 2);
        }

        return view('reports.movieIncomeReport', [
            'title' => 'Movie Ticket Income Report',
            'movieIncomeData' => $movieIncomeData,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData
        ]);
    }

    // Snack Demand Report ********************************************************************
    public function snackDemandReport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $isCustomRange = false;

        if ($startDate && $endDate) {
            try {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
                $isCustomRange = true;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Invalid date format');
            }
        }

        $today = Carbon::now()->format('Y-m-d');

        if ($isCustomRange) {
            $demandRangeStart = $startDate;
            $demandRangeEnd = $endDate;
            $salesRangeStart = $startDate;
            $salesRangeEnd = $endDate;
        } else {
            $demandRangeStart = $today;
            $demandRangeEnd = Carbon::now()->addDays(7)->format('Y-m-d');
            $salesRangeStart = '2000-01-01';
            $salesRangeEnd = $today;
        }

        // Snacks needed for shows in the demand range
        $demandShows = Shows::with(['movies', 'bookings' => function ($query) {
                $query->where('payment_status', 'PAID')->with('bookingSnacks.snack');
            }])
            ->whereBetween('date', [$demandRangeStart, $demandRangeEnd])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        $upcomingSnackDemand = [];
        foreach ($demandShows as $show) {
            $snackTotals = [];

            foreach ($show->bookings as $booking) {
                foreach ($booking->bookingSnacks as $bookingSnack) {
                    if (!$bookingSnack->snack) {
                        continue;
                    }

                    $snackId = $bookingSnack->snack->idsnacks;

                    if (!isset($snackTotals[$snackId])) {
                        $snackTotals[$snackId] = [
                            'snack_name' => $bookingSnack->snack->name,
                            'snack_size' => $bookingSnack->snack->size,
                            'quantity_needed' => 0
                        ];
                    }

                    $snackTotals[$snackId]['quantity_needed'] += $bookingSnack->quantity;
                }
            }

            foreach ($snackTotals as $snackTotal) {
                $upcomingSnackDemand[] = (object) [
                    'show_date' => $show->date,
                    'show_time' => $show->time,
                    'movie_name' => $show->movies ? $show->movies->name : 'Unknown Movie',
                    'snack_name' => $snackTotal['snack_name'],
                    'snack_size' => $snackTotal['snack_size'],
                    'quantity_needed' => $snackTotal['quantity_needed']
                ];
            }
        }

        // Snack sales for shows in the sales range
        $salesShows = Shows::with(['movies', 'bookings' => function ($query) {
                $query->where('payment_status', 'PAID')->with('bookingSnacks');
            }])
            ->whereBetween('date', [$salesRangeStart, $salesRangeEnd])
            ->orderBy('date', 'desc')
            ->get();

        if (!$isCustomRange) {
            $salesShows = $salesShows->take(30);
        }

        $passedShowSnackSales = [];
        foreach ($salesShows as $show) {
            $totalSnacksSold = 0;
            $snackIncome = 0;

            foreach ($show->bookings as $booking) {
                foreach ($booking->bookingSnacks as $bookingSnack) {
                    $totalSnacksSold += $bookingSnack->quantity;
                    $snackIncome += $bookingSnack->quantity * $bookingSnack->price;
                }
            }

            if ($totalSnacksSold > 0) {
                $passedShowSnackSales[] = (object) [
                    'show_date' => $show->date,
                    'show_time' => $show->time,
                    'movie_name' => $show->movies ? $show->movies->name : 'Unknown Movie',
                    'total_snacks_sold' => $totalSnacksSold,
                    'snack_income' => $snackIncome
                ];
            }
        }

        // Top 3 snacks all-time (always unfiltered by date)
        $allBookingSnacks = Bookings::with('bookingSnacks.snack')
            ->where('payment_status', 'PAID')
            ->get()
            ->pluck('bookingSnacks')
            ->flatten();

        $snackTotalsAllTime = [];
        foreach ($allBookingSnacks as $bookingSnack) {
            if (!$bookingSnack->snack) {
                continue;
            }

            $snackId = $bookingSnack->snack->idsnacks;

            if (!isset($snackTotalsAllTime[$snackId])) {
                $snackTotalsAllTime[$snackId] = [
                    'snack_name' => $bookingSnack->snack->name,
                    'snack_size' => $bookingSnack->snack->size,
                    'total_quantity_sold' => 0,
                    'total_income' => 0
                ];
            }

            $snackTotalsAllTime[$snackId]['total_quantity_sold'] += $bookingSnack->quantity;
            $snackTotalsAllTime[$snackId]['total_income'] += $bookingSnack->quantity * $bookingSnack->price;
        }

        usort($snackTotalsAllTime, function ($a, $b) {
            return $b['total_quantity_sold'] <=> $a['total_quantity_sold'];
        });

        $topSnacksAllTime = array_slice($snackTotalsAllTime, 0, 3);

        $chartLabels = [];
        $chartData = [];
        foreach ($topSnacksAllTime as $snack) {
            $chartLabels[] = $snack['snack_name'] . ' (' . $snack['snack_size'] . ')';
            $chartData[] = $snack['total_quantity_sold'];
        }

        return view('reports.snackDemandReport', [
            'title' => 'Snack Demand Report',
            'upcomingSnackDemand' => $upcomingSnackDemand,
            'passedShowSnackSales' => $passedShowSnackSales,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'isCustomRange' => $isCustomRange
        ]);
    }
}