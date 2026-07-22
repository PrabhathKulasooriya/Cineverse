@extends('moviePage_include.main')
@section('pageSpecificStyles')
<link rel="stylesheet" href="{{ asset('css/ticketPage.css') }}">

@endsection

@section('pageSpecificContent')
    <div class="ticket-page-main">
        
                    @if(session('success'))
                        <div class="alert alert-success text-center position-fixed fade show" style="top: 100px; right: 20px; z-index: 1000; min-width: 350px;">
                            <i class="fa fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger text-center position-fixed fade show" style="top: 100px; right: 20px; z-index: 1000; min-width: 350px;">
                            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
      

        @if(isset($booking))
        <div class="ticket-container">
            
            <div class="ticket-header">
                <h2> <img src="{{ URL::asset('assets/images/logo/logo_1.png')}}" alt="" height="50"> Cineverse Cinema</h2>
                <p class="mb-0">Seat Booking Confirmation</p>
            </div>

            <div class="ticket-body">
                
                <div class="ticket-row">
                    <span class="ticket-label">Booking ID</span>
                    <span class="ticket-value">BK{{ $booking['booking_id']}}</span>
                </div>

                <div class="ticket-row">
                    <span class="ticket-label">Customer Name</span>
                    <span class="ticket-value">{{ $booking['customer_name']  }}</span>
                </div>

                <div class="ticket-row">
                    <span class="ticket-label">Movie</span>
                    <span class="ticket-value">{{ $booking['movie_name'] ?? 'Movie ID: ' . $booking['movieId'] }}</span>
                </div>

                <div class="ticket-row">
                    <span class="ticket-label">Date</span>
                    <span class="ticket-value">{{Carbon\Carbon::parse($booking['show_date'])->format('d-m-Y')}}</span>
                </div>

                <div class="ticket-row">
                    <span class="ticket-label">Show Time</span>
                    <span class="ticket-value">{{Carbon\Carbon::parse($booking['show_time'])->format('h:i A')}}</span>
                </div>


                <div class="ticket-row">
                    <span class="ticket-label">Seats</span>
                    <div class="seat-numbers">

                            @foreach($booking['seats'] as $seat)
                                <span class="seat-badge">{{ $seat->row}}{{ $seat->number }}</span>
                            @endforeach    

                    </div>
                </div>

                @if(isset($booking['booking_snacks']) && $booking['booking_snacks']->count() > 0)
                <div class="ticket-row">
                    <span class="ticket-label">Snacks</span>
                    <div class="snack-order-list">
                        @foreach($booking['booking_snacks'] as $item)
                        <div class="snack-order-item">
                            <span class="snack-order-name">
                                {{ $item->snack->name }}
                                @if($item->snack->size !== 'REGULAR')
                                    ({{ $item->snack->size }})
                                @endif
                            </span>
                            <span class="snack-order-qty">x{{ $item->quantity }}</span>
                            <span class="snack-order-price">Rs. {{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="ticket-row">
                    <span class="ticket-label">Payment Status</span>
                    <span class="ticket-status">{{$booking['payment_status']}}</span>
                </div>

                @if(isset($booking['booking_snacks']) && $booking['booking_snacks']->count() > 0)
                <div class="ticket-row ticket-amounts-row">
                    <div class="amounts-breakdown">
                        <div class="amount-line">
                            <span class="ticket-label">Tickets</span>
                            <span class="ticket-value">LKR {{ number_format($booking['amount'], 2) }}</span>
                        </div>
                        <div class="amount-line">
                            <span class="ticket-label">Snacks</span>
                            <span class="ticket-value">LKR {{ number_format($booking['grandTotal'] - $booking['amount'], 2) }}</span>
                        </div>
                        <div class="amount-line grand-total-line">
                            <span class="ticket-label">Total Amount</span>
                            <span class="ticket-value ticket-amount">LKR {{ number_format($booking['grandTotal'], 2) }}</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="ticket-row">
                    <span class="ticket-label">Total Amount</span>
                    <span class="ticket-value ticket-amount">LKR {{ number_format($booking['amount'], 2) }}</span>
                </div>
                @endif

                <div class="text-center">

                    @if(Auth::check() &&( Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 3))
                    
                        <div class="d-flex flex-row justify-content-between">
                        <form action="{{ route('verifyTicket') }}" method="post" >
                            @csrf
                            <input type="hidden" name="bookingId" value="{{$booking['booking_id']}}">
                            <button class="btn btn-ticket-page mx-2" type="submit">
                                <i class="fa fa-download" aria-hidden="true"></i> Confirm entry
                            </button>
                        </form>

                        <a href="{{route('printTicket', ['booking_id' => $booking['booking_id']])}}" target="_blank">
                            <button class="btn btn-ticket-page mx-2">
                                <i class="fa fa-download" aria-hidden="true"></i> Print Ticket
                            </button>
                        </a>
                        </div> 
                        
                        <div class="">
                        <a href="{{route('shows')}}" >
                            <button class="btn btn-ticket-page mx-2">
                                <i class="fa fa-download" aria-hidden="true"></i> Back to all shows
                            </button>
                        </a>

                        <a href="{{ route('seatSelection', ['show_id' => $booking['showId']]) }}"> 
                            <button class="btn btn-ticket-page mx-2">
                                <i class="fa fa-download" aria-hidden="true"></i> Book Again
                            </button>
                        </a>
                        </div>
                    @else
                        
                        <a href="{{route('downloadTicket', ['booking_id' => $booking['booking_id']])}}" target="_blank">
                            <button class="btn btn-ticket-page ">
                                <i class="fa fa-download" aria-hidden="true"></i> Get Ticket
                            </button>
                        </a>
                    @endif
                </div>

            </div>

            <div class="ticket-footer">
                <p class="mb-1">Please ensure you keep this ticket and bring it with you on the scheduled movie date</p>
                <p class="mb-1">Please arrive 15 minutes before showtime</p>
                <p class="mb-0">Contact us : info@cineverse.com</p>
                <p class="mb-0">Call us : 0115123456</p>
                
            </div>

        </div>
        @else
            <div class="ticket-container">
                <div class="ticket-header">
                    <h2><i class="fa fa-exclamation-triangle"></i> No Ticket Found</h2>
                </div>
                <div class="ticket-body text-center">
                    <p class="text-white">No booking information available.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">Book a Ticket</a>
                </div>
            </div>
        @endif

    </div>
@endsection

@section('pageSpecificScript')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $(".alert").fadeOut("slow", function() {
                    $(this).remove();
                });
            }, 3000); 
        });
    </script>
@endsection
