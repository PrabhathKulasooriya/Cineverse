<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Snack Demand Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2, h3 { margin: 0; padding: 0; }
        .header { margin-bottom: 18px; }
        .section { margin-top: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
        th { background: #eee; }
        .snack-box { margin-bottom: 6px; }
        .snack-item { display: inline-block; margin-right: 6px; padding: 2px 6px; border-radius: 4px; background: #f0f0f0; }
        .text-muted { color: #666; }
        .top-snacks { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .top-snack-card { width: 48%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background: #fafafa; }
        .top-snack-card strong { display: block; margin-bottom: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Snack Demand Report</h1>
        <p class="text-muted">Today's snack needs, upcoming daily summary, and all-time popular snacks.</p>
    </div>

    <div class="section">
        <h2>Today's Snack Need</h2>
        <table>
            <thead>
                <tr>
                    <th width="20%">Date</th>
                    <th width="20%">Time</th>
                    <th width="20%">Movie</th>
                    <th>Snacks Needed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todaySnackDemand as $show)
                    <tr>
                        <td>{{ $show->show_date }}</td>
                        <td>{{ $show->show_time }}</td>
                        <td>{{ $show->movie_name }}</td>
                        <td>
                            @foreach($show->snacks as $snackName => $sizes)
                                <div class="snack-box">
                                    <strong>{{ $snackName }}</strong>
                                    @foreach($sizes as $item)
                                        <span class="snack-item">{{ $item->size }}: {{ $item->qty }}</span>
                                    @endforeach
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">No snack pre-orders for today.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Upcoming Snack Demand</h2>
        <table>
            <thead>
                <tr>
                    <th width="25%">Date</th>
                    <th>Daily Snack Totals</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcomingSnackDemand as $day)
                    <tr>
                        <td>{{ $day->show_date }}</td>
                        <td>
                            @foreach($day->snacks as $snackName => $sizes)
                                <div class="snack-box">
                                    <strong>{{ $snackName }}</strong>
                                    @foreach($sizes as $item)
                                        <span class="snack-item">{{ $item->size }}: {{ $item->qty }}</span>
                                    @endforeach
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-muted">No snack pre-orders for the coming week.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>All-Time Most Popular Snacks</h2>
        <div class="top-snacks">
            @forelse($topSnacksAllTime as $top)
                <div class="top-snack-card">
                    <strong>{{ $top->snack_name }}</strong>
                    <div>Units Sold: {{ number_format($top->total_sold) }}</div>
                </div>
            @empty
                <div class="text-muted">No historical snack sales data available.</div>
            @endforelse
        </div>
    </div>
</body>
</html>
