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
        <h3 class="page-title">Snack Demand Report</h3>
    </li>
</ul>
<div class="clearfix"></div>
</nav>
</div>

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="col-lg-12">

            <div class="card m-b-20 today-card" style="border: 2px solid #B22222;">
                <div class="card-body">
                    <h5 class="section-heading text-danger">Today's Snack Need</h5>
                    @include('reports.partials.snackDemandTable', ['shows' => $todaySnackDemand, 'emptyText' => 'No snack pre-orders for today.'])
                </div>
            </div>

            <div class="card m-b-20" style="border: none; box-shadow: none;">
                <div class="card-body">
                    <h5 class="section-heading">Snacks Needed — Next 7 Days (Daily Summary)</h5>
                    @include('reports.partials.dailySnackDemandTable', ['days' => $upcomingSnackDemand, 'emptyText' => 'No snack pre-orders for the coming week.'])
                </div>
            </div>

            <div class="card m-b-20" style="border: none; box-shadow: none;">
                <div class="card-body">
                    <h5 class="section-heading">All-Time Most Popular Snacks</h5>
                    <div class="row mt-4">
                        @foreach($topSnacksAllTime as $top)
                            <div class="col-md-3">
                                <div class="top-snack-card border">
                                    <p>{{ $top->snack_name }}</p>
                                    <h3>{{ number_format($top->total_sold) }} <small style="font-size: 14px;">Units</small></h3>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('includes/footer_start')
@include('includes/footer_end')