<header id="header">
    <div class="container-fluid">
        <div class="navbar" >
            <div class="nav-item nav-item-logo">

                <a href="{{ URL::asset('/')}}" id="logo">
                    <img src="{{ URL::asset('assets/images/logo/logo_3.png')}}" height="80" alt="logo">
                </a>
    
                <div class="search-wrapper">
                    <div class="search-container">
                        <form class="search-form" action="{{ url('/search') }}" method="GET">
                            <input class="search-input" type="search" name="query" placeholder="Search movies..." aria-label="Search">
                            <button class="search-button" type="submit">
                                <i class='fa fa-search'></i>
                            </button>
                        </form>
                    </div>
                </div>
                </div>

            {{-- links --}}
            <div class="nav-item nav-item-menu">
            <div class="navigation-row">
                <nav id="navigation">
                    <button type="button" class="navbar-toggle" aria-expanded="false" aria-controls="nav-box"> 
                        <i class="fa fa-bars"></i> 
                    </button>
                    <div class="nav-box navbar-collapse">
                        <ul class="navigation-menu nav navbar-nav navbars" id="nav">
                            <li data-menuanchor="slide01" class="active"><a href="#slide01">Home</a></li>
                            <li data-menuanchor="slide02"><a href="#slide02">Movies</a></li>
                            <li data-menuanchor="slide03"><a href="#slide03">About Us</a></li>
                            <li data-menuanchor="slide04"><a href="#slide04">Contact Us</a></li>
                            
                            @if (Auth::check()) 

                                <!-- If the user is logged in -->
                                <!-- admin or employee view -->
                                @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==1 || 
                                    \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==2 ||
                                    \Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==3)
                                <li data-menuanchor="slide06"><a href="dashboard">Dashboard</a></li>
                                @endif

                                {{-- customer view --}}
                                @if(\Illuminate\Support\Facades\Auth::user()->user_role_iduser_role==4)
                                <li data-menuanchor="slide06"><a href="myAccount">My Account</a></li>
                                @endif
                            @else
                                 <!-- If the user is not logged in -->
                                 <li data-menuanchor="slide05"><a href="clientSignup">Sign Up</a></li>
                                 <li data-menuanchor="slide05"><a href="signin">Log In</a></li>
                            @endif

                        </ul>
                    </div>
                </nav>
            </div>
            </div>
        </div>
    </div>

</header>