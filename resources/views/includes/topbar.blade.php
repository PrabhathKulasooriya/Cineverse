            <!-- Start right Content here -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">

                    <!-- Top Bar Start -->
                    <div class="topbar">

                        <nav class="navbar-custom">

                            <ul class="list-inline float-right mb-0">
                                
                                <!-- Fullscreen -->
                                <li class="list-inline-item dropdown notification-list hidden-xs-down">
                                    <a class="nav-link waves-effect" href="#" id="btn-fullscreen">
                                        <i class="mdi mdi-fullscreen noti-icon"></i>
                                    </a>
                                </li>

                               
                                <!-- User-->
                                <li class="list-inline-item dropdown notification-list">
                                    <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button"
                                       aria-haspopup="false" aria-expanded="false">
                                       <img src="{{ URL::asset('assets/images/users/avatar-1.png')}}" height="20" alt="user" class="rounded-circle">

                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                        <a class="dropdown-item" href="{{route('myAccount')}}"><i class="dripicons-user text-muted"></i> {{Auth::user()->first_name}}</a>
                                        <a class="dropdown-item" href="{{route('home')}}"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
                                        @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==4)
                                        <a class="dropdown-item" href="{{route('upcomingBookings')}}" class="dropdown-item"><i class="fa fa-calendar-check-o text-muted"></i> Bookings</a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logout"><i class="dripicons-exit text-muted"></i> Logout</a>
                                    </div>
                                </li>
                            </ul>
