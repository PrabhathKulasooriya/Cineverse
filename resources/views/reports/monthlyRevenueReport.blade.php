@include('includes/header_start')
<link href="{{ URL::asset('assets/css/jquery.notify.css')}}" rel="stylesheet" type="text/css">
<meta name="csrf-token" content="{{ csrf_token() }}"/>
@include('includes/header_end')

<ul class="list-inline menu-left mb-0">
    <li class="list-inline-item">
        <button type="button" class="button-menu-mobile open-left waves-effect">
            <i class="ion-navicon"></i>
        </button>
    </li>
    <li class="hide-phone list-inline-item app-search">
        <h3 class="page-title">Monthly Revenue Report</h3>
    </li>
</ul>
<div class="clearfix"></div>
</nav>
</div>

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card m-b-20">
                <div class="card-body">

                    <form action="{{ route('monthlyRevenueReport') }}" method="get">
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label>Select Date Range:</label>
                                <div class="input-daterange input-group">
                                    <label class="btn">From -</label>
                                    <input type="date" class="form-control" name="startDate" value="{{ request('startDate') }}">
                                    <label class="btn">To -</label>
                                    <input type="date" class="form-control" name="endDate" value="{{ request('endDate') }}">
                                </div>
                            </div>
                            <div class="form-group col-md-2" style="padding-top: 28px">
                                <button type="submit" class="btn btn-md btn-primary waves-effect">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <h4>Seat Revenue</h4>
                                <h3>LKR {{ number_format($grandSeatTotal, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <h4>Snack Revenue</h4>
                                <h3>LKR {{ number_format($grandSnackTotal, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <h4>Grand Total</h4>
                                <h3>LKR {{ number_format($grandTotal, 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <canvas id="monthlyRevenueChart" height="90"></canvas>

                    <table class="table table-striped table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>MONTH</th>
                                <th>SEAT REVENUE (LKR)</th>
                                <th>SNACK REVENUE (LKR)</th>
                                <th>TOTAL REVENUE (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tableRows as $row)
                                <tr>
                                    <td>{{ $row['month'] }}</td>
                                    <td>{{ number_format($row['seat_revenue'], 2) }}</td>
                                    <td>{{ number_format($row['snack_revenue'], 2) }}</td>
                                    <td><strong>{{ number_format($row['total_revenue'], 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No data found for the selected date range.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

@include('includes/footer_start')

<script src="{{ URL::asset('assets/plugins/chartjs/Chart.min.js')}}"></script>

<script type="text/javascript">
    var chartLabels = @json($chartLabels);
    var chartSeatData = @json($chartSeatData);
    var chartSnackData = @json($chartSnackData);
    var chartTotalData = @json($chartTotalData);

    var ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
    var monthlyRevenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Seat Revenue',
                    data: chartSeatData,
                    borderColor: '#B22222',
                    backgroundColor: 'rgba(178, 34, 34, 0.1)',
                    fill: true
                },
                {
                    label: 'Snack Revenue',
                    data: chartSnackData,
                    borderColor: '#FFD700',
                    backgroundColor: 'rgba(255, 215, 0, 0.1)',
                    fill: true
                },
                {
                    label: 'Total Revenue',
                    data: chartTotalData,
                    borderColor: '#2C2C2C',
                    backgroundColor: 'rgba(44, 44, 44, 0.05)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{ ticks: { beginAtZero: true } }]
            }
        }
    });
</script>

@include('includes/footer_end')