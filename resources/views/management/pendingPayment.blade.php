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
        <div class="col-lg-12">
            <div class="card m-b-20">
                <div class="card-body">


                <br/>
                @if(session('success'))
                <div class="alert alert-success text-center ticket-success-container alert-dismissible" id="ticket-success-container">
                    <i class="fa fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                 <div class="alert alert-danger alert-dismissible text-center floating-alert" role="alert">
                     <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


             <div class="table-rep-plugin">
                <div class="table-responsive b-0" data-pattern="priority-columns">
                    <table id="datatable" class="table table-striped table-bordered"
                           cellspacing="0"
                           width="100%">
            
                        <thead>
                            <tr>
                                <th>BOOKING ID</th>
                                <th>MOVIE</th>
                                <th>DATE</th>
                                <th>TIME</th>
                                <th>USER</th>
                                <th>AMOUNT</th>
                                <th>PAYMENT STATUS</th>
                                <th>OPTIONS</th>
                            </tr>
                        </thead>
            
                        <tbody>
                            
                            @if(!empty($pendingPayments) )
                                @foreach($pendingPayments as $pendingPayment)
                                    <tr>
                                        <td>BK{{ $pendingPayment['booking_id'] }}</td> 
                                        <td>{{ $pendingPayment['movie']}}</td> 
                                        <td>{{\Carbon\Carbon::parse($pendingPayment['date'])->format('d M Y')}}</td> 
                                        <td>{{\Carbon\Carbon::parse($pendingPayment['time'])->format('h:i A')}}</td> 
                                        <td>{{ $pendingPayment['userName'] }}</td> 
                                        <td>{{ $pendingPayment['amount'] }}</td> 
                                        <td><p class="text-danger ">{{ $pendingPayment['payment_status'] }}</p></td> 
                                        <td class="d-flex">
                                            <form action="{{ route('cancelPayment') }}" method="post" id="payNowForm">
                                                @csrf
                                                <input type="hidden" name="bookingData" value="{{$pendingPayment['booking_id']}}">
                                                <button class="btn btn-sm btn-danger waves-effect" id="payNowBtn">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> 
                                                </button>
                                            </form>
                                            @if(Auth::user()->idmaster_user == $pendingPayment['master_user_idmaster_user'])
                                            <form action="{{ route('payPending') }}" method="post" id="payNowForm">
                                                @csrf
                                                <input type="hidden" name="bookingData" value="{{json_encode($pendingPayment)}}">
                                                <button class="btn btn-sm btn-success waves-effect ml-2" id="payNowBtn" >
                                                    <i class="fa fa-usd" aria-hidden="true"></i> Pay Now
                                                </button>
                                            </form>
                                            @endif
                                        </td>
        
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center">No pending payments found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>


              <!--Data Table End-->


            

                </div>
            </div>
        </div>
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



@include('includes/footer_end')