@extends('moviePage_include.main')
@section('pageSpecificStyles')
<link rel="stylesheet" href="{{ asset('css/ticketPage.css') }}">

@endsection

@section('pageSpecificContent')
    <div class="ticket-page-main">
        
        @if(session('success'))
                <div class="alert alert-success text-center ticket-success-container alert-dismissible" id="ticket-success-container">
                    <i class="fa fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        
        @if(session('error'))
                 <div class="alert alert-danger alert-dismissible text-center ticket-success-container " role="alert">
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

                <div class="ticket-row">
                    <span class="ticket-label">Payment Status</span>
                    <span class="ticket-status">{{$booking['payment_status']}}</span>
                </div>

                <div class="ticket-row">
                    <span class="ticket-label">Total Amount</span>
                    <span class="ticket-value ticket-amount">LKR {{ number_format($booking['amount'], 2) }}</span>
                </div>

                <div class="text-center">

                    @if(Auth::check() &&( Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 3))
                        <a href="{{route('printTicket', ['booking_id' => $booking['booking_id']])}}" target="_blank">
                            <button class="btn btn-ticket-page ">
                                <i class="fa fa-download" aria-hidden="true"></i> Print Ticket
                            </button>
                        </a>
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

@endsection