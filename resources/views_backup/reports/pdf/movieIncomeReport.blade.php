<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movie Ticket Income Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2 { margin: 0; padding: 0; }
        .header { margin-bottom: 18px; }
        p { margin: 4px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .text-right { text-align: right; }
        .subtext { font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Movie Ticket Income Report</h1>
        <p class="subtext">Showing filtered table results for {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Movie</th>
                <th>Start Date</th>
                <th class="text-right">Days on Screen</th>
                <th class="text-right">No. of Shows</th>
                <th class="text-right">Ticket Income (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movieIncomeData as $movie)
                <tr>
                    <td>{{ $movie->movie_name }}</td>
                    <td>{{ $movie->start_date ?? '-' }}</td>
                    <td class="text-right">{{ $movie->days_screening }}</td>
                    <td class="text-right">{{ $movie->no_of_shows }}</td>
                    <td class="text-right">{{ number_format($movie->total_income, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-right">No data available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
