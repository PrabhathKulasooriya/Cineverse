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
                    <div class="alert alert-success  alert-dismissible" id="ticket-success-container">
                        <i class="fa fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>

            <form action="{{ route('findBookingCheck') }}" method="post" id="bookingVerificationForm">
                <div class="ticket-verification-input">
                    <span>Email:
                    {{csrf_field()}}
                    <input type="email" name="bookingEmail" id="bookingEmail" placeholder="test@test.com" >
                    <button class="btn-verify" id="verifyBtn">Find</button>
                    </span>
                
                </div>
            </form>
    
    
            @if(isset($bookings))

            <!--Data Table Start-->
            <div class="col-lg-12">
            <div class="table-rep-plugin">
                <div class="card m-b-20">
                    <div class="card-body">
                    <div class="table-responsive b-0" data-pattern="priority-columns">


                    <table id="datatable"   class="table table-striped table-bordered"
                        cellspacing="0"
                        width="100%">

                        <thead>
                            <tr>
                                <th>BOOKING ID</th>
                                <th>MOVIE NAME</th>
                                <th>SHOW DATE</th>
                                <th>SHOW TIME</th>
                                <th>AMOUNT</th>
                                <th>CREATED AT</th>
                                <th>SEATS</th>
                                <th>OPTIONS</th>
                                
                            </tr>
                        </thead>

                        <tbody>

                    
                        @if(isset($bookings))
                            @if(count($bookings)>0)
                                @foreach($bookings as $booking)

                                    <tr>
                                        <td>BK{{$booking['booking_id']}}</td>
                                        <td>{{$booking['movie_name']}}</td>
                                        <td>{{\Carbon\Carbon::parse($booking['show_date'])->format('d M Y')}}</td>
                                        <td>{{\Carbon\Carbon::parse($booking['show_time'])->format('h:i A')}}</td>
                                        <td>LKR. {{number_format($booking['amount'], 2)}}</td>
                                        <td>{{\Carbon\Carbon::parse($booking['created_at'])->format('H:i A | d-M-Y ')}}</td>
                                        <td>
                                            @foreach($booking['seats'] as $seat)
                                             <span class="seat-badge">{{$seat['row']}}{{$seat['number']}}</span>
                                            @endforeach
                                        </td>
                                        
                                        <td>
                                            <form action="{{route('verifyTicket')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="bookingId" value="{{$booking['booking_id']}}">
                                                <button type='submit' class="btn btn-sm btn-success" title="Verify Ticket">
                                                    <i class="fa fa-download" aria-hidden="true"></i> Get Ticket
                                                </button>
                                            </form>
                                        </td>
                                       
                                    </tr>

                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center">Search Booking Id</td>
                                </tr>
                            @endif
                        @else
                            <tr>
                                <td colspan="8" class="text-center">No bookings available</td>
                            </tr>
                        @endif

                        </tbody>

                    </table>

                </div>
            </div>
            </div>
            </div>
            </div>
            <!--Data Table End-->

            @else
                <div>
                    <h3><i class="fa fa-exclamation-triangle"></i> No bookings found.</h3>                   
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

    //change sorting of datatable
    $.fn.dataTable.ext.type.order['id-num-pre'] = function (d) {
            var match = d.match(/\d+/);
            return match ? parseInt(match[0], 10) : 0;
        };
        
        $.fn.dataTable.ext.type.detect.unshift(function (d) {
            return (typeof d === 'string' && d.match(/^\d{1,2}-\d{1,2}-\d{4}$/)) ? 'custom-date' : null;
        });

        $.fn.dataTable.ext.type.order['custom-date-pre'] = function (d) {
            if (!d) return 0;
            var parts = d.split('-');
            return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
        };

        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable().destroy();
            }
            
            $('#datatable').DataTable({
                "order": [2, 'asc'],
                "columnDefs": [
                    { "orderable": false, "targets": [ 4,5,6,-1] },
                    { "type": "id-num", "targets": 0 },
                    { "type": "custom-date", "targets": 2 }
                ]
            });
        });


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
    const bookingVerificationForm = document.getElementById('bookingVerificationForm');

    verifyBtn.addEventListener('click', function () {
        preventDefault();
        bookingVerificationForm.submit();
    });


</script>

@include('includes/footer_end')