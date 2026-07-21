@include('includes/header_start')
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}"/>
@include('includes/header_end')

<ul class="list-inline menu-left mb-0">
    <li class="list-inline-item">
        <button type="button" class="button-menu-mobile open-left waves-effect">
            <i class="ion-navicon"></i>
        </button>
    </li>
    <li class="hide-phone list-inline-item app-search">
        <h3 class="page-title">Movie Ticket Income Report</h3>
    </li>
</ul>
<div class="clearfix"></div>
</nav>
</div>

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card m-b-20" style="border: none; box-shadow: none;">
                <div class="card-body">


                    <h5 class="section-heading ">Best Selling Active Movies (Avg. Income Per Show)</h5>
                    <div class="chart-wrapper">
                        <canvas id="topMoviesChart" height="400"></canvas>
                    </div>

                    <hr class="mt-4"/>
                    
                    <h4>Income Report - Movies </h2>
                    <form action="{{ route('movieIncomeReport') }}" method="get" class="d-flex flex-row align-items-center justify-content-between">
                        <div class="row date-search-row">
                            <div class="form-group col-md-9">
                                <label>Select Date Range</label>
                                <div class="input-daterange input-group">
                                    <label class="btn">From -</label>
                                    <input type="date" class="form-control" name="startDate" value="{{ $startDate }}">
                                    <label class="btn">To -</label>
                                    <input type="date" class="form-control" name="endDate" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="form-group col-md-2" style="padding-top: 28px">
                                <button type="submit" class="btn btn-md btn-primary waves-effect">Search</button>
                            </div>
                        </div>
                        <div class="">
                            <a href="{{ route('movieIncomeReport.pdf', request()->query()) }}" class="btn btn-md btn-secondary waves-effect ml-2">Export PDF</a>
                        </div>
                    </form>

                    <div class="active-range-label">
                        <strong>Showing table results for:</strong> {{ $startDate }} to {{ $endDate }}
                      
                    </div>

                    <div class="table-responsive">
                        
                        <table class="table table-striped table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>MOVIE</th>
                                    <th>START DATE</th>
                                    <th>DAYS ON SCREEN</th>
                                    <th>NO. OF SHOWS</th>
                                    <th>TICKET INCOME (LKR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movieIncomeData as $movie)
                                    <tr>
                                        <td>{{ $movie->movie_name }}</td>
                                        <td>{{ $movie->start_date ?? '-' }}</td>
                                        <td>{{ $movie->days_screening }}</td>
                                        <td>{{ $movie->no_of_shows }}</td>
                                        <td>{{ number_format($movie->total_income, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">No data found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    

                </div>
            </div>
        </div>
    </div>
</div>

@include('includes/footer_start')

<script src="{{ URL::asset('assets/plugins/chartjs/Chart.min.js')}}"></script>
<script type="text/javascript">
    var chartLabels = @json($chartLabels);
    var chartData = @json($chartData);
    var chartShowCounts = @json($chartShowCounts);

    // Map labels to arrays to create multi-line text on the X-axis
    var multiLineLabels = chartLabels.map(function(label, index) {
        return [label, 'Shows: ' + chartShowCounts[index]];
    });

    var ctx = document.getElementById('topMoviesChart').getContext('2d');
    var topMoviesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: multiLineLabels, 
            datasets: [{
                label: 'Avg. Income Per Show (LKR)',
                data: chartData,
                backgroundColor: '#B22222',
                barThickness: 40, 
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [{ 
                    ticks: { beginAtZero: true },
                    scaleLabel: {
                        display: true,
                        labelString: 'Average Income per show(LKR)'
                    }
                }],
                xAxes: [{ 
                    ticks: { 
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0
                    } 
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return 'Avg Income: LKR ' + Number(tooltipItem.yLabel).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                }
            }
        }
    });
</script>

@include('includes/footer_end')
