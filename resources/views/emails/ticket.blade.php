<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: auto;
        }
        .ticket-container {
            background: #fff;
            border: 2px dashed #333;
            padding: 20px;
            max-width: 430px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .ticket-header h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        .ticket-header p {
            margin: 5px 0;
            font-size: 16px;
        }
        .ticket-body {
            margin-bottom: 20px;
            text-align: center;
        }
        .ticket-row {
            margin-bottom: 10px;
            width: 90%;
            display: table;
            table-layout: fixed;
            margin-left: auto;
            margin-right: auto;
        }
        .ticket-label {
            font-weight: bold;
            display: table-cell;
            width: 30%;
            vertical-align: top;
            padding-right: 10px;
            text-align: left;
        }
        .ticket-value {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            text-align: right;
        }
        .seats-container {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            text-align: right;
        }
        .seat-badge {
            display: inline-block;
            background: #B22222;
            color: #fff;
            padding: 5px 10px;
            margin: 3px 3px 3px 0;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        /* Snack styles */
        .snack-list {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            text-align: right;
        }
        .snack-item {
            margin-bottom: 4px;
            font-size: 13px;
            color: #333;
        }
        .snack-item-name {
            color: #555;
        }
        .snack-item-price {
            font-weight: bold;
            color: #B22222;
        }
        .snack-divider {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 8px auto;
            width: 90%;
        }
        .amount-breakdown {
            width: 90%;
            margin: 0 auto 10px;
        }
        .amount-line {
            display: table;
            table-layout: fixed;
            width: 100%;
            margin-bottom: 4px;
        }
        .amount-line-label {
            display: table-cell;
            width: 60%;
            text-align: left;
            font-size: 13px;
            color: #555;
        }
        .amount-line-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-size: 13px;
            color: #333;
        }
        .grand-total-label {
            font-weight: bold;
            font-size: 15px;
            color: #333;
        }
        .grand-total-value {
            font-weight: bold;
            font-size: 15px;
            color: #B22222;
        }

        .qr-code-container {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .qr-code-container img {
            max-width: 100px;
            height: auto;
        }
        .qr-code-container p {
            margin: 10px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        .ticket-footer {
            font-size: 13px;
            color: #555;
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            text-align: center;
        }
        .ticket-footer p {
            margin: 5px 0;
            text-align: center;
        }
        .ticket-amount {
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <img src="{{ asset('assets/images/logo/logo_3.png') }}" alt="Cineverse" height="60">
            <p>Seat Booking Confirmation</p>
        </div>

        <div class="ticket-body">
            <div class="ticket-row">
                <span class="ticket-label">Booking ID:</span>
                <span class="ticket-value">BK{{ $booking['booking_id'] }}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Customer:</span>
                <span class="ticket-value">{{ $booking['customer_name'] }}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Movie:</span>
                <span class="ticket-value">{{ $booking['movie_name'] }}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Date:</span>
                <span class="ticket-value">{{ Carbon\Carbon::parse($booking['show_date'])->format('d-m-Y') }}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Show Time:</span>
                <span class="ticket-value">{{ Carbon\Carbon::parse($booking['show_time'])->format('h:i A') }}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Seats:</span>
                <div class="seats-container">
                    @foreach($seats as $seat)
                        <span class="seat-badge">{{ $seat->row }}{{ $seat->number }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Snacks section --}}
            @if(isset($booking['booking_snacks']) && count($booking['booking_snacks']) > 0)
            <div class="ticket-row">
                <span class="ticket-label">Snacks:</span>
                <div class="snack-list">
                    @foreach($booking['booking_snacks'] as $item)
                    <div class="snack-item">
                        <span class="snack-item-name">
                            {{ $item->snack->name }}
                            @if(strtoupper($item->snack->size) !== 'REGULAR')
                                ({{ $item->snack->size }})
                            @endif
                            x{{ $item->quantity }}
                        </span>
                        &nbsp;
                        <span class="snack-item-price">
                            Rs. {{ number_format($item->price * $item->quantity, 2) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="ticket-row">
                <span class="ticket-label">Status:</span>
                <span class="ticket-value">{{ $booking['payment_status'] }}</span>
            </div>

            {{-- Amount breakdown if snacks exist --}}
            @if(isset($booking['booking_snacks']) && count($booking['booking_snacks']) > 0)
            <hr class="snack-divider">
            <div class="amount-breakdown">
                <div class="amount-line">
                    <span class="amount-line-label">Tickets</span>
                    <span class="amount-line-value">LKR {{ number_format($booking['amount'], 2) }}</span>
                </div>
                <div class="amount-line">
                    <span class="amount-line-label">Snacks</span>
                    <span class="amount-line-value">
                        LKR {{ number_format(($booking['grandTotal'] ?? $booking['amount']) - $booking['amount'], 2) }}
                    </span>
                </div>
                <div class="amount-line">
                    <span class="amount-line-label grand-total-label">Total Paid</span>
                    <span class="amount-line-value grand-total-value">
                        LKR {{ number_format($booking['grandTotal'] ?? $booking['amount'], 2) }}
                    </span>
                </div>
            </div>
            @else
            <div class="ticket-row">
                <span class="ticket-label">Total:</span>
                <span class="ticket-value ticket-amount">LKR {{ number_format($booking['amount'], 2) }}</span>
            </div>
            @endif

        </div>

        @if(isset($qrCode))
        <div class="qr-code-container">
            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
        </div>
        @endif

        <div class="ticket-footer">
            <p>Please bring this ticket with you on the scheduled date.</p>
            <p>Arrive at least 15 minutes before showtime.</p>
            <p>Contact: info@cineverse.com | Call: 0115123456</p>
        </div>
    </div>
</body>
</html>