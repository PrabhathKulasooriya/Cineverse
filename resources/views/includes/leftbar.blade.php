<!-- Loader -->
<div id="preloader">
    <div id="status">
        <div class="spinner"></div>
    </div>
</div>

<!-- Begin page -->
<div id="wrapper">

    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">

        <!-- LOGO -->
        <div class="topbar-left">
            <div >

                <a href="{{ route('home')}}" class="logo"><img src="{{ URL::asset('assets/images/logo/logo_2.png')}}"
                                                                     height="80" alt="logo"></a>
            </div>
        </div>

        <div class="sidebar-inner slimscrollleft">
            <div id="sidebar-menu">

                <ul>

                    <li class="menu-title">Main</li>

                        @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1 || 
                        \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==2 || 
                        \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==3)
                            <li>
                                <a href="dashboard" class="waves-effect"><i
                                            class="fa fa-area-chart"></i><span>Dashboard </span></a>
                            </li>
                        @endif
                            
                            <li>
                                <a href="myAccount" class="waves-effect"><i
                                            class="fa fa-user"></i><span>My Account</span></a>
                            </li>


                    {{-- MOVIES --}}

                    
                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1 || 
                    \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==2 || 
                    \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==3)

                        <li class="menu-title">Movies</li>

                        <li>
                            <a href="{{route('shows')}}" class="waves-effect">
                                <i class="fa fa-window-maximize"></i>
                                <span>Upcoming Shows</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{route('movies')}}" class="waves-effect">
                                <i class="fa fa-film"></i>
                                <span>Movies</span>
                            </a>
                        </li>


                    @endif

                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1 || 
                    \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==2 )
                    

                    <li>
                        <a href="{{route('movieSlider')}}" class="waves-effect">
                            <i class="fa fa-picture-o"></i>
                            <span>Movie Slider</span>
                        </a>
                    </li>
                    
                    @endif
                    

                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1)

                    <li>
                        <a href="{{route('showtimes')}}" class="waves-effect">
                            <i class="fa fa-clock-o"></i>
                            <span>Showtimes</span>
                        </a>
                    </li>

                    @endif


                    {{-- Bookings --}}  
                    <li class="menu-title">Bookings</li>

                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==4)
                  
                        
                        <li>
                            <a href="upcomingBookings" class="waves-effect">
                                <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                <span>Upcoming Bookings</span>
                            </a>
                        </li>

                        <li>
                            <a href="pastBookings" class="waves-effect">
                                <i class="fa fa-history" aria-hidden="true"></i>
                                <span>Past Bookings</span>
                            </a>
                        </li>

                        <li>
                            <a href="customerPendingPayments" class="waves-effect">
                                <i class="fa fa-hourglass" aria-hidden="true"></i>
                                <span>Pending Payments</span>
                            </a>
                        </li>

                    @endif

                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1 ||
                    \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==2 ||
                   \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==3)

                        
                    
                        <li>
                            <a href="ticketVerification" class="waves-effect">
                                <i class="fa fa-ticket" aria-hidden="true"></i><span>Ticket Verification</span>
                            </a>
                        </li>
                    
                    
                        <li>
                            <a href="findBooking" class="waves-effect">
                                <i class="fa fa-search" aria-hidden="true"></i><span>Find Booking</span>
                            </a>
                        </li>

                        <li>
                            <a href="pendingPayments" class="waves-effect">
                                <i class="fa fa-clock-o" aria-hidden="true"></i><span>Pending Payments</span>
                            </a>
                        </li>




                    @endif


                    {{-- MASTER FILES --}}

                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1 ||
                        \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==2 ||
                        \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==3)

                    <li class="menu-title">Master Files</li>


                        <li>
                            <a href="clientManagement" class="waves-effect"><i
                                        class="fa fa-user"></i><span>Client Management</span></a>
                        </li>

                        

                    @endif


                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1)

                        <li>
                            <a href="employeeManagement" class="waves-effect"><i
                                        class="fa fa-user"></i><span>Staff Management </span></a>
                        </li>

                        
                        <li>
                            <a href="screenedMovies" class="waves-effect"><i
                                        class="fa fa-user"></i><span>Screened Movies </span></a>
                        </li>

                        <li>
                            <a href="{{route('screenedShows')}}" class="waves-effect">
                                <i class="fa fa-window-maximize"></i>
                                <span>Screened Shows</span>
                            </a>
                        </li>

                    @endif




                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1 )

                    <li class="menu-title">Ticket Settings</li>
                    <li>
                        <a href="ticketSettings" class="waves-effect">
                            <i class="fa fa-ticket" aria-hidden="true"></i><span>Ticket Prices</span>
                        </a>
                    </li>

                    @endif

      

                    @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1 )


                    <li class="menu-title">Reports</li>

                        <li>
                            <a href="revenueReport" class="waves-effect"><i
                                        class="fa fa-file-text-o"></i><span>Revenue Report</span></a>
                        </li> 


                         <li>
                            <a href="clientReport" class="waves-effect"><i
                                        class="fa fa-file-text-o"></i><span>Client Report</span></a>
                        </li> 

                    @endif









                </ul>

            </div>




            <div class="clearfix"></div>

        </div> <!-- end sidebarinner -->
    </div> <!-- Left Sidebar End -->