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
        <h3 class="page-title">Snack Demand Report</h3>
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
                    <h5 class="mb-3">Snacks Needed — Next 7 Days</h5>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SHOW DATE</th>
                                <th>TIME</th>
                                <th>MOVIE</th>
                                <th>SNACK</th>
                                <th>SIZE</th>
                                <th>QUANTITY NEEDED</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingSnackDemand as $row)
                                <tr>
                                    <td>{{ $row->show_date }}</td>
                                    <td>{{ $row->show_time }}</td>
                                    <td>{{ $row->movie_name }}</td>
                                    <td>{{ $row->snack_name }}</td>
                                    <td>{{ $row->snack_size }}</td>
                                    <td><strong>{{ $row->quantity_needed }}</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No snack pre-orders for the coming week.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card m-b-20">
                <div class="card-body">
                    <h5 class="mb-3">Snack Sales — Passed Shows</h5>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SHOW DATE</th>
                                <th>TIME</th>
                                <th>MOVIE</th>
                                <th>SNACKS SOLD</th>
                                <th>SNACK INCOME (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($passedShowSnackSales as $row)
                                <tr>
                                    <td>{{ $row->show_date }}</td>
                                    <td>{{ $row->show_time }}</td>
                                    <td>{{ $row->movie_name }}</td>
                                    <td>{{ $row->total_snacks_sold }}</td>
                                    <td>{{ number_format($row->snack_income, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No passed shows with snack sales.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card m-b-20">
                <div class="card-body">
                    <h5 class="mb-3">Top 3 Selling Snacks (All Time)</h5>
                    <canvas id="topSnacksChart" height="100"></canvas>
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

    var ctx = document.getElementById('topSnacksChart').getContext('2d');
    var topSnacksChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
                backgroundColor: ['#B22222', '#FFD700', '#2C2C2C']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

@include('includes/footer_end')