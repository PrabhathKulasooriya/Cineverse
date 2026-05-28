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
        <h3 class="page-title">{{$title}}</h3>
    </li>
    <li class="hide-phone list-inline-item app-search">
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

                    <form action="{{ route('clientReport') }}" method="get">
                        <div class="row">
                            {{ csrf_field() }}
                            <div class="form-group col-md-5">
                                <label>Select Date Range:</label>
                                <div class="input-daterange input-group" >
                                    <label for="startDate" class="btn ">From -</label><input type="date" class="form-control" name="startDate" value="{{ request('startDate') }}" id="startDate">
                                    <label for="startDate" class="btn ">To -</label><input type="date" class="form-control" name="endDate" value="{{ request('endDate') }}" id="endDate">
                                </div>
                            </div>

                            <div class="form-group col-md-2" style="padding-top: 28px">
                                <button type="submit" class="btn btn-md btn-primary waves-effect">Search</button>
                            </div>
                        </div>
                    </form>

                    @if(isset($reportData) && $reportData['users'])
                        <!-- Guest Bookings Summary -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-secondary">
                                    <h5><i class="fa fa-info-circle"></i> Guest Bookings Summary 
                                        @if(request('startDate') && request('endDate'))
                                            ({{ request('startDate') }} to {{ request('endDate') }})
                                        @endif
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Total Guest Bookings:</strong> {{ $reportData['guest_bookings'] ?? 0 }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Guest Revenue:</strong> LKR {{ number_format($reportData['guest_revenue'] ?? 0, 2) }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Average per Guest:</strong> LKR {{ $reportData['guest_bookings'] > 0 ? number_format(($reportData['guest_revenue'] ?? 0) / $reportData['guest_bookings'], 2) : '0.00' }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>% of Total Revenue:</strong> {{ $reportData['total_revenue'] > 0 ? round((($reportData['guest_revenue'] ?? 0) / $reportData['total_revenue']) * 100, 1) : 0 }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                                <table class="table table-striped table-bordered" id="datatable-buttons"
                                       cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>USER ID</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>EMAIL</th>
                                        <th>TOTAL BOOKINGS</th>
                                        <th>TOTAL SPENT (LKR)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($reportData['users']) )
                                        @foreach($reportData['users'] as $user)
                                            <tr>
                                                <td>{{ $user['user_id']  }}</td>
                                                <td>{{ $user['name']  }}</td>
                                                <td>{{ $user['email']  }}</td>
                                                <td>{{ $user['bookings']}}</td>
                                                <td>{{ number_format($user['total_spent'] , 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">No registered user data found for the selected date range.</td>
                                        </tr>
                                    @endif

                                    </tbody>
                                    <tbody>
                                        @if(isset($reportData['registered_users_total']))
                                        <tr style="background-color: #f8f9fa; font-weight: bold; color: #495057;">
                                            <td><b>REGISTERED USERS TOTAL BOOKINGS</b></td>
                                            <td></td>
                                            <td></td>
                                            <td><b>{{ $reportData['registered_users_total']['bookings'] ?? 0 }}</b></td>
                                            <td><b>{{ number_format($reportData['registered_users_total']['revenue'] ?? 0, 2) }}</b></td>
                                        </tr>

                                        <tr style="background-color: #e9ecef; font-weight: bold; ">
                                            <td><b>GRAND TOTAL (Including Guests)</b></td>
                                            <td></td>
                                            <td></td>
                                            <td><b>{{ ($reportData['registered_users_total']['bookings'] ?? 0) + ($reportData['guest_bookings'] ?? 0) }}</b></td>
                                            <td><b>{{ number_format($reportData['total_revenue'] ?? 0, 2) }}</b></td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                                <div class="mt-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Registered Users Bookings</h5>
                                                    <p class="mb-1"><strong>Total Users:</strong> {{ $reportData['total_users'] ?? 0 }}</p>
                                                    <p class="mb-1"><strong>Total Bookings:</strong> {{ $reportData['registered_users_total']['bookings'] ?? 0 }}</p>
                                                    <p class="mb-1"><strong>Total Revenue:</strong> LKR {{ number_format($reportData['registered_users_total']['revenue'] ?? 0, 2) }}</p>
                                                    <p class="mb-0"><strong>Avg per User:</strong> LKR {{ $reportData['total_users'] > 0 ? number_format(($reportData['registered_users_total']['revenue'] ?? 0) / $reportData['total_users'], 2) : '0.00' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Guest Users Bookings</h5>
                                                    <p class="mb-1"><strong>Total Bookings:</strong> {{ $reportData['guest_bookings'] ?? 0 }}</p>
                                                    <p class="mb-1"><strong>Total Revenue:</strong> LKR {{ number_format($reportData['guest_revenue'] ?? 0, 2) }}</p>
                                                    <p class="mb-1"><strong>Avg per Booking:</strong> LKR {{ $reportData['guest_bookings'] > 0 ? number_format(($reportData['guest_revenue'] ?? 0) / $reportData['guest_bookings'], 2) : '0.00' }}</p>
                                                    <p class="mb-0"><strong>% of Total:</strong> {{ $reportData['total_revenue'] > 0 ? round((($reportData['guest_revenue'] ?? 0) / $reportData['total_revenue']) * 100, 1) : 0 }}%</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Top Spenders</h5>
                                                    @if(isset($reportData['top_spenders']) && count($reportData['top_spenders']) > 0)
                                                        @foreach($reportData['top_spenders'] as $index => $spender)
                                                            @if($index < 3)
                                                                <p class="mb-1"><strong>{{ $index + 1 }}{{ $index == 0 ? 'st' : ($index == 1 ? 'nd' : 'rd') }}:</strong> {{ $spender['name'] }} - LKR {{ number_format($spender['total_spent'], 0) }}</p>
                                                            @endif
                                                        @endforeach
                                                        @if(isset($reportData['lowest_spender']))
                                                            <p class="mb-0"><strong>Lowest:</strong> {{ $reportData['lowest_spender']['name'] }} - LKR {{ number_format($reportData['lowest_spender']['total_spent'], 0) }}</p>
                                                        @endif
                                                    @else
                                                        <p class="mb-0">No spending data available</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <h5><i class="fa fa-exclamation-triangle"></i> No Data Available</h5>
                            <p>Please select a date range and click "Search" to generate the revenue report.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div><!-- container -->
</div> <!-- Page content Wrapper -->

@include('includes/footer_start')

<!-- Plugins js -->
<script src="{{ URL::asset('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}" type="text/javascript"></script>

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




    //change of datatable
        $.fn.dataTable.ext.type.order['id-num-pre'] = function (d) {
            var match = d.match(/\d+/);
            return match ? parseInt(match[0], 10) : 0;
        };
        

        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#datatable-buttons')) {
                $('#datatable-buttons').DataTable().destroy();
            }
            
            $('#datatable-buttons').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                order: [[4, 'desc']],
                columnDefs: [
                    { type: 'id-num', targets: 0 }
                ]
            });
        });
    
</script>

@include('includes/footer_end')