@include('includes/header_start')

<link href="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">

<!-- Plugins css -->
<link href="{{ URL::asset('assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet"/>
<link href="{{ URL::asset('assets/css/custom_checkbox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/css/jquery.notify.css')}}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/css/mdb.css')}}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('css/ticketPage.css') }}">

<meta name="csrf-token" content="{{ csrf_token() }}"/>


@include('includes/header_end')

<!-- Page title -->
<ul class="list-inline menu-left mb-0">
    <li class="list-inline-item">
        <button type="button" class="button-menu-mobile open-left waves-effect">
            <i class="ion-navicon"></i>
        </button>
    </li>
    <li class="hide-phone list-inline-item app-search">
        <h3 class="page-title">{{ $title }}</h3>
    </li>
</ul>

<div class="clearfix"></div>
</nav>

</div>
<!-- Top Bar End -->

<!-- ==================
     PAGE CONTENT START
     ================== -->

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row ticketpage-alert-container">
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
        </div>        

        @if(auth()->user()->user_role_iduser_role == 1 || auth()->user()->user_role_iduser_role == 2 || auth()->user()->user_role_iduser_role == 3)
            
        <form action="{{ route('verifyTicket') }}" method="post" id="ticketVerificationForm">
                <div class="ticket-verification-input" id='ticketVerificationInput'>
                    <div>
                    <span>BK
                        {{csrf_field()}}
                        <input type="number" name="bookingId" id="bookingId" placeholder="123456789" >
                    </span>
                        <button class="btn-verify" id="verifyBtn">Verify</button>
                    </div>

                </div>
            </form>
        @endif

        @if(isset($booking))
        <div class="ticket-verification-page-main">
        
            
    
           
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
                        <span class="ticket-label">Customer</span>
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
    
                                @foreach($seats as $seat)
                                    <span class="seat-badge">{{ $seat->row}}{{ $seat->number }}</span>
                                @endforeach    
    
                        </div>
                    </div>
    
                    <div class="ticket-row">
                        <span class="ticket-label">Status</span>
                        <span class="ticket-status">{{ $booking['payment_status']}}</span>
                    </div>
    
                    <div class="ticket-row">
                        <span class="ticket-label">Total Amount</span>
                        <span class="ticket-value ticket-amount">LKR {{ number_format($booking['amount'], 2) }}</span>
                    </div>   

                    
                    <div class="text-center">
                        @if(auth()->user()->user_role_iduser_role == 1 || auth()->user()->user_role_iduser_role == 3 || auth()->user()->user_role_iduser_role == 4)
                            <a href="{{route('printTicket', ['booking_id' => $booking['booking_id']])}}" target="_blank">
                                <button class="btn btn-ticket-page ">
                                    <i class="fa fa-download" aria-hidden="true"></i> Get Ticket
                                </button>
                            </a>
                        @endif

                        <form id="emailTicketForm" action="{{ route('sendTicketEmail') }}" method="post" >
                            @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking['booking_id']}}">
                        <button class="btn btn-ticket-page btn-email" type="submit">
                            <i class="fa fa-share" aria-hidden="true"></i> Send via Email
                        </button>
                        </form>

                    </div>
                    
                    <div class="ticket-footer">
                        <p class="mb-1">Please ensure you keep this ticket and bring it with you on the scheduled movie date</p>
                        <p class="mb-1">Please arrive 15 minutes before showtime</p>
                        <p class="mb-0">Contact us : info@cineverse.com</p>
                        <p class="mb-0">Call us : 0115123456</p>
                        
                    </div>
                </div>
            </div>   
        </div>
        @else
                <div>
                    <h3><i class="fa fa-exclamation-triangle"></i> Enter Booking Id.</h3> 
                </div>
        @endif
    </div> <!-- container -->

</div> <!-- Page content Wrapper -->

</div> <!-- content -->




@include('includes/footer_start')

<!-- Plugins js -->
<script src="{{ URL::asset('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}"
        type="text/javascript"></script>

<!-- Plugins Init js -->
<script src="{{ URL::asset('assets/pages/form-advanced.js')}}"></script>

<!-- Required datatable js -->
<script src="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
<!-- Buttons examples -->
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.buttons.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/jszip.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/pdfmake.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/vfs_fonts.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.html5.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.print.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.colVis.min.js')}}"></script>
<!-- Responsive examples -->
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.responsive.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.js')}}"></script>

<script src="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
<script src="{{ URL::asset('assets/pages/sweet-alert.init.js')}}"></script>

<!-- Datatable init js -->
<script src="{{ URL::asset('assets/pages/datatables.init.js')}}"></script>

<!-- Parsley js -->
<script type="text/javascript" src="{{ URL::asset('assets/plugins/parsleyjs/parsley.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/bootstrap-notify.js')}}"></script>
<script src="{{ URL::asset('assets/js/jquery.notify.min.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    });

    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });



    const verifyBtn = document.getElementById('verifyBtn');
    const ticketVerificationForm = document.getElementById('ticketVerificationForm');

    verifyBtn.addEventListener('click', function () {
        preventDefault();
        ticketVerificationForm.submit();
    });

    //Download Ticket****************************************************************
    function triggerDownload() {

    const elementsToHide = [
        '.btn-download',
        '.btn-ticket-page', 
        '.alert',
        '.ticket-success-container',
        'nav',
        'header',
        'footer',
        '.navbar'
    ];
    
    elementsToHide.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => {
            el.style.display = 'none';
        });
    });
    
    
    window.print();
    
    
    setTimeout(() => {
        elementsToHide.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                el.style.display = '';
            });
        });
    }, 500);
}



    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.btn-download')?.addEventListener('click', function() {
            triggerDownload();
        });
    });

</script>

@include('includes/footer_end')