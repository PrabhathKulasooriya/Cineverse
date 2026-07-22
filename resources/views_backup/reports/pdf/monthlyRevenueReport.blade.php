<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Revenue Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2, h3 { margin: 0; padding: 0; }
        .header { margin-bottom: 18px; }
        .header p { margin: 4px 0 0; }
        .summary-grid { display: flex; justify-content: space-between; margin: 18px 0; }
        .summary-card { width: 32%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background: #f8f8f8; }
        .summary-card strong { display: block; margin-bottom: 6px; font-size: 12px; color: #333; }
        .summary-card .value { font-size: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Revenue Report</h1>
        <p>Reporting Period: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <strong>Seat Revenue</strong>
            <div class="value">LKR {{ number_format($grandSeatTotal, 2) }}</div>
        </div>
        <div class="summary-card">
            <strong>Snack Revenue</strong>
            <div class="value">LKR {{ number_format($grandSnackTotal, 2) }}</div>
        </div>
        <div class="summary-card">
            <strong>Grand Total</strong>
            <div class="value">LKR {{ number_format($grandTotal, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-right">Seat Revenue (LKR)</th>
                <th class="text-right">Snack Revenue (LKR)</th>
                <th class="text-right">Total Revenue (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tableRows as $row)
                <tr>
                    <td>{{ $row['month'] }}</td>
                    <td class="text-right">{{ number_format($row['seat_revenue'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['snack_revenue'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['total_revenue'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-right">No data available for the selected range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
