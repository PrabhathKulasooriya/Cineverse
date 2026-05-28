@include('includes/header_start')

<link href="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/css/custom_checkbox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/css/jquery.notify.css')}}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
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
        <h3 class="page-title">Revenue Report</h3>
    </li>
</ul>

<div class="clearfix"></div>
</nav>

</div>
<!-- Top Bar End -->

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card m-b-20">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="fas fa-chart-line mr-2"></i>{{ $reportTitle ?? 'Revenue Report' }}
                    </h4>

                    <form action="{{ route('revenueReport') }}" method="get">
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

                    @if(isset($reportData) && $reportData)
                        
                        <!-- Summary Statistics -->
                        @if(isset($summaryStats))
                        <div class="row mb-4">
                            @foreach($summaryStats as $stat)
                            <div class="col-md-{{ $stat['col_size'] ?? 4 }}">
                                <div class="stats-card">
                                    <h4><i class="{{ $stat['icon'] ?? 'fa fa-chart-bar' }} mr-2"></i>{{ $stat['title'] }}</h4>
                                    @foreach($stat['items'] as $item)
                                    <div class="stat-item">
                                        <span>{{ $item['label'] }}:</span>
                                        <span><strong>{{ $item['value'] }}</strong></span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Alert Summary -->
                        @if(isset($alertSummary))
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-secondary">
                                    <h5><i class="{{ $alertSummary['icon'] ?? 'fas fa-info-circle' }}"></i> {{ $alertSummary['title'] }}
                                        @if(request('startDate') && request('endDate'))
                                            ({{ request('startDate') }} to {{ request('endDate') }})
                                        @endif
                                    </h5>
                                    <div class="row">
                                        @foreach($alertSummary['items'] as $item)
                                        <div class="col-md-{{ $item['col_size'] ?? 3 }}">
                                            <strong>{{ $item['label'] }}:</strong> {{ $item['value'] }}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Main Data Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="datatable-buttons">
                                <thead>
                                    <tr>
                                        <th>SHOW ID</th>
                                        <th>MOVIE NAME</th>
                                        <th>DATE & TIME</th>
                                        <th>PRIME SEATS</th>
                                        <th>NORMAL SEATS </th>
                                        <th>TOTAL REVENUE (LKR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($tableData) && count($tableData) > 0)
                                        @foreach($tableData as $row)
                                        <tr>
                                            @foreach($row as $key => $cell)
                                            <td class="{{ $cell['class'] ?? '' }}">
                                                @if(isset($cell['value']))
                                                    {{ $cell['value'] }}
                                                @else
                                                    {{ $cell }}
                                                @endif
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ count($tableHeaders) }}" class="text-center">
                                                {{ $noDataMessage ?? 'No data found for the selected date range.' }}
                                            </td>
                                        </tr>
                                    @endif

                                    <!-- Additional Totals/Summary Rows -->
                                    @if(isset($summaryRows))
                                        @foreach($summaryRows as $summaryRow)
                                        <tr class="{{ $summaryRow['class'] ?? 'summary-row' }}">
                                            @foreach($summaryRow['data'] as $cell)
                                            <td class="{{ $cell['class'] ?? '' }}">
                                                @if(isset($cell['value']))
                                                    {!! $cell['value'] !!}
                                                @else
                                                    {!! $cell !!}
                                                @endif
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                
                                @if(isset($tableFooter))
                                <tfoot>
                                    @foreach($tableFooter as $footerRow)
                                    <tr class="{{ $footerRow['class'] ?? 'summary-row' }}">
                                        @foreach($footerRow['data'] as $cell)
                                        <td class="{{ $cell['class'] ?? '' }}">
                                            @if(isset($cell['value']))
                                                {!! $cell['value'] !!}
                                            @else
                                                {!! $cell !!}
                                            @endif
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tfoot>
                                @endif
                            </table>
                        </div>

                        <!-- Additional Cards Section -->
                        @if(isset($additionalCards))
                        <div class="mt-4">
                            <div class="row">
                                @foreach($additionalCards as $card)
                                <div class="col-md-{{ $card['col_size'] ?? 4 }}">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $card['title'] }}</h5>
                                            @foreach($card['content'] as $content)
                                            <p class="mb-1">
                                                <strong>{{ $content['label'] }}:</strong> {{ $content['value'] }}
                                            </p>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    @else
                        <div class="alert alert-secondary">
                            <h5><i class="fa fa-exclamation-triangle"></i> No Data Available</h5>
                            <p>{{ $noDataText ?? 'Please select a date range and click "Search" to generate the revenue report.' }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@include('includes/footer_start')

<!-- Required plugins -->
<script src="{{ URL::asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.buttons.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/jszip.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/pdfmake.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/vfs_fonts.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.html5.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.print.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.colVis.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.responsive.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/parsleyjs/parsley.min.js')}}"></script>
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

       
        $('#date-range').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        
        $('#datatable-buttons').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf', 'print'],
            responsive: true,
            order: []
        });

       
        $(document).on("wheel", "input[type=number]", function (e) {
            $(this).blur();
        });
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