<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use PDF;

class ReportController extends Controller
{
    // Monthly Revenue Report *****************************************************************
    public function monthlyRevenueReport(Request $request)
    {
        $data = $this->buildMonthlyRevenueReportData($request);
        return view('reports.monthlyRevenueReport', $data);
    }

    public function monthlyRevenueReportPdf(Request $request)
    {
        $data = $this->buildMonthlyRevenueReportData($request);
        $pdf = PDF::loadView('reports.pdf.monthlyRevenueReport', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('monthly-revenue-report.pdf');
    }

    private function buildMonthlyRevenueReportData(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $isCustomRange = false;

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
            $isCustomRange = true;
        } else {
            $endDate = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
        }

        $monthlyTotals = DB::table('payments')
            ->join('bookings', 'payments.bookings_booking_id', '=', 'bookings.booking_id')
            ->select(
                DB::raw("DATE_FORMAT(payments.created_at, '%Y-%m') as month"),
                DB::raw('SUM(bookings.amount) as seat_revenue'),
                DB::raw('SUM(payments.amount) as total_revenue')
            )
            ->where('bookings.payment_status', 'PAID')
            ->whereBetween('payments.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $chartLabels = [];
        $chartSeatData = [];
        $chartSnackData = [];
        $chartTotalData = [];
        $tableRows = [];
        $grandSeatTotal = 0;
        $grandSnackTotal = 0;

        foreach ($monthlyTotals as $row) {
            $seatRevenue = $row->seat_revenue ? $row->seat_revenue : 0;
            $totalRevenue = $row->total_revenue ? $row->total_revenue : 0;
            $snackRevenue = $totalRevenue - $seatRevenue;

            $chartLabels[] = Carbon::createFromFormat('Y-m', $row->month)->format('M Y');
            $chartSeatData[] = round($seatRevenue, 2);
            $chartSnackData[] = round($snackRevenue, 2);
            $chartTotalData[] = round($totalRevenue, 2);

            $tableRows[] = [
                'month' => Carbon::createFromFormat('Y-m', $row->month)->format('M Y'),
                'seat_revenue' => $seatRevenue,
                'snack_revenue' => $snackRevenue,
                'total_revenue' => $totalRevenue
            ];

            $grandSeatTotal += $seatRevenue;
            $grandSnackTotal += $snackRevenue;
        }

        return [
            'title' => 'Monthly Revenue Report',
            'tableRows' => $tableRows,
            'chartLabels' => $chartLabels,
            'chartSeatData' => $chartSeatData,
            'chartSnackData' => $chartSnackData,
            'chartTotalData' => $chartTotalData,
            'grandSeatTotal' => $grandSeatTotal,
            'grandSnackTotal' => $grandSnackTotal,
            'grandTotal' => $grandSeatTotal + $grandSnackTotal,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isCustomRange' => $isCustomRange
        ];
    }

    // Movie Ticket Income Report *************************************************************
    public function movieIncomeReport(Request $request)
    {
        $data = $this->buildMovieIncomeReportData($request);
        return view('reports.movieIncomeReport', $data);
    }

    public function movieIncomeReportPdf(Request $request)
    {
        $data = $this->buildMovieIncomeReportData($request);
        $pdf = PDF::loadView('reports.pdf.movieIncomeReport', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('movie-income-report.pdf');
    }

    private function buildMovieIncomeReportData(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $isCustomRange = false;

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
            $isCustomRange = true;
        } else {
            $endDate = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::now()->subYear()->format('Y-m-d');
        }

        $revenueData = DB::table('bookings')
            ->join('movies', 'bookings.movies_movie_id', '=', 'movies.movie_id')
            ->select(
                'movies.movie_id',
                'movies.name as movie_name',
                DB::raw('SUM(bookings.amount) as total_income')
            )
            ->where('bookings.payment_status', 'PAID')
            ->whereBetween('bookings.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('movies.movie_id', 'movies.name')
            ->orderBy('total_income', 'desc')
            ->get()
            ->keyBy('movie_id');

        $showStats = DB::table('shows')
            ->select(
                'movies_movie_id',
                DB::raw('COUNT(show_id) as no_of_shows'),
                DB::raw('MIN(date) as start_date'),
                DB::raw('DATEDIFF(MAX(date), MIN(date)) + 1 as days_screening')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('movies_movie_id')
            ->get()
            ->keyBy('movies_movie_id');

        $movieIncomeData = [];
        foreach ($revenueData as $movieId => $movie) {
            $stats = isset($showStats[$movieId]) ? $showStats[$movieId] : null;

            $movieIncomeData[] = (object) [
                'movie_id' => $movieId,
                'movie_name' => $movie->movie_name,
                'total_income' => $movie->total_income,
                'start_date' => $stats ? $stats->start_date : null,
                'days_screening' => $stats ? $stats->days_screening : 0,
                'no_of_shows' => $stats ? $stats->no_of_shows : 0
            ];
        }

        $today = Carbon::now()->format('Y-m-d');

        $activeMovieIds = DB::table('shows')
            ->where('date', '>=', $today)
            ->pluck('movies_movie_id')
            ->unique()
            ->toArray();

        $allTimeStats = [];
        if (!empty($activeMovieIds)) {
            $allTimeRevenueData = DB::table('bookings')
                ->join('movies', 'bookings.movies_movie_id', '=', 'movies.movie_id')
                ->select('movies.movie_id', 'movies.name as movie_name', DB::raw('SUM(bookings.amount) as total_income'))
                ->where('bookings.payment_status', 'PAID')
                ->whereIn('movies.movie_id', $activeMovieIds)
                ->groupBy('movies.movie_id', 'movies.name')
                ->get()
                ->keyBy('movie_id');

            $allTimeShowData = DB::table('shows')
                ->select('movies_movie_id', DB::raw('COUNT(show_id) as no_of_shows'))
                ->whereIn('movies_movie_id', $activeMovieIds)
                ->where('date', '<=', $today)
                ->groupBy('movies_movie_id')
                ->get()
                ->keyBy('movies_movie_id');

            foreach ($activeMovieIds as $movieId) {
                $income = $allTimeRevenueData->has($movieId) ? $allTimeRevenueData[$movieId]->total_income : 0;
                $shows = $allTimeShowData->has($movieId) ? $allTimeShowData[$movieId]->no_of_shows : 0;

                if ($allTimeRevenueData->has($movieId)) {
                    $name = $allTimeRevenueData[$movieId]->movie_name;
                } else {
                    $movieObj = DB::table('movies')->where('movie_id', $movieId)->first();
                    $name = $movieObj ? $movieObj->name : 'Unknown';
                }

                $avgIncome = $shows > 0 ? ($income / $shows) : 0;

                if ($shows > 0) {
                    $allTimeStats[] = (object) [
                        'movie_name' => $name,
                        'avg_income' => $avgIncome,
                        'shows' => $shows
                    ];
                }
            }

            usort($allTimeStats, function($a, $b) {
                return $b->avg_income <=> $a->avg_income;
            });

            $allTimeStats = array_slice($allTimeStats, 0, 5);
        }

        $chartLabels = [];
        $chartData = [];
        $chartShowCounts = [];
        foreach ($allTimeStats as $stat) {
            $chartLabels[] = $stat->movie_name;
            $chartData[] = round($stat->avg_income, 2);
            $chartShowCounts[] = $stat->shows;
        }

        return [
            'title' => 'Movie Ticket Income Report',
            'movieIncomeData' => $movieIncomeData,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'chartShowCounts' => $chartShowCounts,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isCustomRange' => $isCustomRange
        ];
    }

    // Snack Demand Report ********************************************************************
    public function snackDemandReport(Request $request)
    {
        $data = $this->buildSnackDemandReportData($request);
        return view('reports.snackDemandReport', $data);
    }

    public function snackDemandReportPdf(Request $request)
    {
        $data = $this->buildSnackDemandReportData($request);
        $pdf = PDF::loadView('reports.pdf.snackDemandReport', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('snack-demand-report.pdf');
    }

    private function buildSnackDemandReportData(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');
        $weekAhead = Carbon::now()->addDays(7)->format('Y-m-d');

        $todaySnackDemand = $this->getSnackDemandForRange($today, $today);
        $upcomingSnackDemand = $this->getDailySnackDemandForRange($tomorrow, $weekAhead);

        $topSnacksAllTime = DB::table('booking_snacks')
            ->join('snacks', 'booking_snacks.snacks_idsnacks', '=', 'snacks.idsnacks')
            ->join('bookings', 'booking_snacks.booking_id', '=', 'bookings.booking_id')
            ->select(
                'snacks.name as snack_name',
                DB::raw('SUM(booking_snacks.quantity) as total_sold')
            )
            ->where('bookings.payment_status', 'PAID')
            ->groupBy('snacks.name')
            ->orderBy('total_sold', 'desc')
            ->limit(4)
            ->get();

        return [
            'title' => 'Snack Demand Report',
            'todaySnackDemand' => $todaySnackDemand,
            'upcomingSnackDemand' => $upcomingSnackDemand,
            'topSnacksAllTime' => $topSnacksAllTime
        ];
    }

    // Helper: Fetches and cleanly groups snacks by show AND snack name
    private function getDailySnackDemandForRange($rangeStart, $rangeEnd)
    {
        $rows = DB::table('booking_snacks')
            ->join('bookings', 'booking_snacks.booking_id', '=', 'bookings.booking_id')
            ->join('shows', 'bookings.shows_show_id', '=', 'shows.show_id')
            ->join('snacks', 'booking_snacks.snacks_idsnacks', '=', 'snacks.idsnacks')
            ->select(
                'shows.date as show_date',
                'snacks.name as snack_name',
                'snacks.size as snack_size',
                DB::raw('SUM(booking_snacks.quantity) as quantity_needed')
            )
            ->whereBetween('shows.date', [$rangeStart, $rangeEnd])
            ->where('bookings.payment_status', 'PAID')
            // We group ONLY by date, snack name, and size here
            ->groupBy('shows.date', 'snacks.name', 'snacks.size')
            ->orderBy('shows.date', 'asc')
            ->get();

        $grouped = [];
        foreach ($rows as $row) {
            $dateKey = $row->show_date;

            // Initialize the date if it doesn't exist
            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = (object) [
                    'show_date' => Carbon::parse($dateKey)->format('Y-m-d l'),
                    'snacks' => []
                ];
            }

            if (!isset($grouped[$dateKey]->snacks[$row->snack_name])) {
                $grouped[$dateKey]->snacks[$row->snack_name] = [];
            }

            $grouped[$dateKey]->snacks[$row->snack_name][] = (object) [
                'size' => $row->snack_size,
                'qty' => $row->quantity_needed
            ];
        }

        return array_values($grouped);
    }

    private function getSnackDemandForRange($rangeStart, $rangeEnd)
    {
        $rows = DB::table('booking_snacks')
            ->join('bookings', 'booking_snacks.booking_id', '=', 'bookings.booking_id')
            ->join('shows', 'bookings.shows_show_id', '=', 'shows.show_id')
            ->join('movies', 'shows.movies_movie_id', '=', 'movies.movie_id')
            ->join('snacks', 'booking_snacks.snacks_idsnacks', '=', 'snacks.idsnacks')
            ->select(
                'shows.show_id',
                'shows.date as show_date',
                'shows.time as show_time',
                'movies.name as movie_name',
                'snacks.name as snack_name',
                'snacks.size as snack_size',
                DB::raw('SUM(booking_snacks.quantity) as quantity_needed')
            )
            ->whereBetween('shows.date', [$rangeStart, $rangeEnd])
            ->where('bookings.payment_status', 'PAID')
            ->groupBy('shows.show_id', 'shows.date', 'shows.time', 'movies.name', 'snacks.name', 'snacks.size')
            ->orderBy('shows.date', 'asc')
            ->orderBy('shows.time', 'asc')
            ->get();

        $grouped = [];
        foreach ($rows as $row) {
            $showId = $row->show_id;

            // Initialize the show if it doesn't exist
            if (!isset($grouped[$showId])) {
                $grouped[$showId] = (object) [
                    'show_date' => Carbon::parse($row->show_date)->format('Y-m-d l'), // 2026-07-12 Sunday
                    'show_time' => Carbon::parse($row->show_time)->format('g.i a'),   // 10.00 am
                    'movie_name' => $row->movie_name,
                    'snacks' => [] 
                ];
            }

           
            if (!isset($grouped[$showId]->snacks[$row->snack_name])) {
                $grouped[$showId]->snacks[$row->snack_name] = [];
            }

            $grouped[$showId]->snacks[$row->snack_name][] = (object) [
                'size' => $row->snack_size,
                'qty' => $row->quantity_needed
            ];
        }

        return array_values($grouped);
    }
}