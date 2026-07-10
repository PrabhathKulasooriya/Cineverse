@include('includes/header_start')
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
            <div class="card m-b-20">
                <div class="card-body">

                    <form action="{{ route('movieIncomeReport') }}" method="get">
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

                    <h5 class="mt-4 mb-3">Top 5 Best-Selling Active Movies</h5>
                    <canvas id="topMoviesChart" height="90"></canvas>

                    <table class="table table-striped table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>MOVIE</th>
                                <th>TOTAL BOOKINGS</th>
                                <th>TOTAL TICKETS</th>
                                <th>TICKET INCOME (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movieIncomeData as $movie)
                                <tr>
                                    <td>{{ $movie->movie_name }}</td>
                                    <td>{{ $movie->total_bookings }}</td>
                                    <td>{{ $movie->total_tickets }}</td>
                                    <td>{{ number_format($movie->total_income, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No data found.</td></tr>
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
    var chartData = @json($chartData);

    var ctx = document.getElementById('topMoviesChart').getContext('2d');
    var topMoviesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Ticket Income (LKR)',
                data: chartData,
                backgroundColor: '#B22222'
            }]
        },
        options: {
            responsive: true,
            legend: { display: false },
            scales: {
                yAxes: [{ ticks: { beginAtZero: true } }]
            }
        }
    });
</script>

@include('includes/footer_end')