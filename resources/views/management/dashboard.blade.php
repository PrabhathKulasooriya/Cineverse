@include('includes/header_start')

<!--Morris Chart CSS -->
<link rel="stylesheet" href="assets/plugins/morris/morris.css">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">


@include('includes/header_end')
<nav>
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
    </ul>

    <div class="clearfix"></div>
</nav>

</div>
<!-- Top Bar End -->

<!-- ==================
     PAGE CONTENT START
     ================== -->


     <div class="page-content-wrapper">

        <div class="header-dashboard">
            <h3>Daily Statistics for {{ $today }}</h3>
        </div>

        <div class="container mt-4">
            <!-- Show Count & Screening Movies Section -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="card shadow-sm h-100 card-info-shows">
                        <div class="card-body">
                            <h5 class="card-title">Number of Shows Today:</h5>
                            <p class="card-text fs-4 fw-bold">{{ $shows->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 card-info-movies">
                        <div class="card-body">
                            <h5 class="card-title">Screening Movies ({{ $movies->count() }}):</h5>
                            <ul class="list-unstyled mb-0">
                                @forelse ($movies as $movie)
                                    <li>{{ $movie->name }}</li>
                                @empty
                                    <li class="text-muted">No movies screening today.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shows Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header card-header-todayshows">
                            <h5 class="mb-0">Today's Shows</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Show ID</th>
                                            <th scope="col">Movie Name</th>
                                            <th scope="col">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($shows as $show)
                                            <tr>
                                                <td data-label="Show ID"><span class="badge">SH-{{ $show->show_id }}</span></td>
                                                <td data-label="Movie Name">{{ $show->movie_name ?? 'N/A' }}</td>
                                                <td data-label="Time">{{ \Carbon\Carbon::parse($show->time)->format('h:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No shows available for today.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    

</div> <!-- content -->

@include('includes/footer_start')

<!--Morris Chart-->
<script src="assets/plugins/morris/morris.min.js"></script>
<script src="assets/plugins/raphael/raphael-min.js"></script>

<script src="assets/pages/dashborad.js"></script>



@include('includes/footer_end')