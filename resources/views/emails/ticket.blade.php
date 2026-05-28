<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: auto
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
        .ticket-amount{
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
                <span class="ticket-value">BK{{$booking['booking_id']}}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Customer:</span>
                <span class="ticket-value">{{$booking['customer_name']}}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Movie:</span>
                <span class="ticket-value">{{$booking['movie_name']}}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Date:</span>
                <span class="ticket-value">{{Carbon\Carbon::parse($booking['show_date'])->format('d-m-Y')}}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Show Time:</span>
                <span class="ticket-value">{{Carbon\Carbon::parse($booking['show_time'])->format('h:i A')}}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Seats:</span>
                <div class="seats-container">
                    @foreach ($seats as $seat )
                        <span class="seat-badge">{{$seat->row}}{{ $seat->number }}</span>
                    @endforeach
                </div>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Status:</span>
                <span class="ticket-value">{{ $booking['payment_status'] }}</span>
            </div>
            <div class="ticket-row">
                <span class="ticket-label">Total :</span>
                <span class="ticket-value ticket-amount">LKR {{ number_format($booking['amount'], 2) }}</span>
            </div>
        </div>

        @if(isset($qrCode))
            <div class="qr-code-container" >
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" >
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