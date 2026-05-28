@extends('moviePage_include.main')

@section('pageSpecificStyles')
<link rel="stylesheet" href="{{ asset('css/moviePage.css') }}">
@endsection



@section('pageSpecificContent')

    <div id="preloader">
        <div class="preloader">
            <div class="preloader-bounce">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <div class=" moviepage-section">
        @if(session('error'))
                    <div class="alert alert-danger alert-dismissible text-center floating-alert " role="alert">
                        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
         
           <div class="top" >
                <div class="overlay"></div>
                
    
                <!-- Section 1: Movie Details  -->
                <div class="movie-details">
                    <h1 class="movie-title">{{$movie->name}}</h1>
                    <div>
                        <span class="movie-duration">{{$movie->formatted_duration}}</span>
                        <span class="dot">&bull;</span>
                        <span class="movie-language">{{$movie->language}}</span>
                        <span class="dot">&bull;</span>
                        <span class="movie-category">{{$movie->category}}</span>
                    </div>
                    <div>
                        <span>{{ \Carbon\Carbon::parse($movie->release_date)->format('l, d  F  Y') }}</span>
                    </div>
                </div>

                <!-- Section 2: Movie Image -->
                <div class="image-container">
                    <img src="{{ URL::asset('movieImages/'.$movie->image) }}" alt="{{$movie->name}}">
                </div>

                <!-- Section 3: Movie Trailer Button -->
                <div class="movie-trailer">
                    <div class="movie-ratings">
                        <i class="fa fa-imdb fa-3x" aria-hidden="true"></i>
                        <span> <i class='fa fa-star' aria-hidden='true'></i> {{$movie->rating}} / 10</span>
                    </div>
                    <a href="{{ $movie->trailer }}" class="movie-trailer-link" id="movie-trailer-link" target="_blank" rel="noopener noreferrer">
                        <i class="fa fa-play fa-3x" aria-hidden="true"></i>
                        <span>Watch Trailer</span>
                    </a>
                </div>
            </div>
              


            {{-- Bottm Section ***************************************************************************--}}
            <div class="bottom">

                @if($shows->isEmpty())
                        <div class="container-noshows">
                            <span>This Movie Has No Scheduled Screenings</span>
                            <p>Screenings will be available soon. Please check back later.</p>
                        </div>

                @else

                {{-- Date Container --}}
                <div class="container-date-main">
                <span>Select  Date</span>
                
                <div class="container-date">
                    
                {{-- Date Selector --}}
                @php
                    $uniqueDates = $shows->groupBy(function($show) {
                    return \Carbon\Carbon::parse($show->date)->format('Y-m-d');
                    });
                @endphp

                <div class="date-selector" id="dateSelector">
                    {{-- Collapsed View (Default) --}}
                    <div class="date-collapsed">
                        
                        @isset($shows)
                        <div class="date-content">
                            <div class="date-day" id="collapsedDay"></div>
                            <div class="date-number" id="collapsedDate"></div>
                            <div class="date-month" id="collapsedMonth"></div>
                        </div>
                        @endisset
                        
                    </div>
                    
                    {{-- Expanded View (On Hover) --}}
                    <div class="date-expanded">
                        <div class="date-row">

                            @foreach ($uniqueDates as $date => $dateShows)
                                <div class="date-item" data-index="{{ $loop->index }}" data-times="">
                                    <div class="date-day">{{ \Carbon\Carbon::parse($date)->format('l') }}</div>
                                    <div class="date-number">{{ \Carbon\Carbon::parse($date)->format('d') }}</div>
                                    <div class="date-month">{{ \Carbon\Carbon::parse($date)->format('F') }}</div>
                                </div>
                            @endforeach

                            
                        </div>
                    </div>

                    <input type="hidden" id="selectedDateInput" name="selected_date" value="">
                </div>

                {{-- Date Selecter End --}}
                {{-- Date Container End --}}


                
                </div>
              </div>

               {{-- Showtimes List --}}
               <div class="container-shows">
                    <span>Available Showtimes</span>
                   
                    <div class="showtimes-list mt-3" id="showtimesList">
                        
                
                    </div>  
                    
               </div>
               @endif
        </div>
            

    </div>
           
    


@endsection



@section('pageSpecificScript')

<script>

document.querySelector('#movie-trailer-link').addEventListener('click', function(event) {
    event.preventDefault(); 
    const linkUrl = this.href; 

    Swal.fire({
        title: 'Open Movie Trailer?',
        text: 'You are about to open an external website. Do you wish to continue?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, open it',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const newWindow = window.open(linkUrl, '_blank', 'noopener,noreferrer');
           
            if (newWindow) {
                newWindow.focus();
            }
        }
    });
});
</script>

{{-- Date & Time Selector Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateItems = document.querySelectorAll('.date-item');
        const dateShows = [];
        const dateSelector = document.getElementById('dateSelector');
        const dateExpanded = document.querySelector('.date-expanded');
        const dateCollapsed = document.querySelector('.date-collapsed');
        const collapsedDay = document.getElementById('collapsedDay');
        const collapsedDate = document.getElementById('collapsedDate');
        const collapsedMonth = document.getElementById('collapsedMonth');
        const selectedDateInput = document.getElementById('selectedDateInput');
        const showtimesList = document.getElementById('showtimesList');
        const showSelectedRoute = "{{ route('seatSelection', ['show_id' => ':show_id']) }}";
        
        let selectedDateIndex = 0;
        let isExpanded = false;
        let isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        // Group shows by date
        const allShows = @json($shows);
        const groupedShows = allShows.reduce((acc, show) => {
            const date = new Date(show.date).toISOString().split('T')[0];
            if (!acc[date]) acc[date] = [];
            acc[date].push(show);
            return acc;
        }, {});

        dateItems.forEach((item, index) => {
            const dayElement = item.querySelector('.date-day');
            const numberElement = item.querySelector('.date-number');
            const monthElement = item.querySelector('.date-month');

            if (dayElement && numberElement) {
                dateShows.push({
                    day: dayElement.textContent,
                    date: numberElement.textContent,
                    month: monthElement.textContent,
                    index: index,
                    rawDate: Object.keys(groupedShows)[index] // match order from backend
                });
            }
        });

        function updateSelectedDate() {
            if (dateShows.length === 0) return;

            const selected = dateShows[selectedDateIndex];
            collapsedDay.textContent = selected.day;
            collapsedDate.textContent = selected.date;
            collapsedMonth.textContent = selected.month;
            selectedDateInput.value = selected.rawDate;

            dateItems.forEach((item, index) => {
                item.classList.toggle('selected', index === selectedDateIndex);
            });

            updateShowtimesList(selected.rawDate);
            
            // Reset selected showtime when date changes
            selectedShowtime = null;
        }

        function showDateCollapsedView() {
            dateExpanded.style.display = 'none';
            dateCollapsed.style.display = 'flex';
            dateSelector.classList.remove('hovered');
            isExpanded = false;
        }

        function showDateExpandedView() {
            dateExpanded.style.display = 'block';
            dateCollapsed.style.display = 'none';
            dateSelector.classList.add('hovered');
            isExpanded = true;
        }

        function toggleDateView() {
            if (isExpanded) {
                showDateCollapsedView();
            } else {
                showDateExpandedView();
            }
        }

        function updateShowtimesList(date) {
            const shows = groupedShows[date] || [];
            showtimesList.innerHTML = '';

            if (shows.length === 0) {
                showtimesList.innerHTML = `<div class="no-showtimes">No showtimes available for this date.</div>`;
                return;
            }

            shows.forEach(show => {
                const time = new Date('1970-01-01T' + show.time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                const a = document.createElement('a');
                a.href = showSelectedRoute.replace(':show_id', show.show_id);

                const div = document.createElement('div');
                div.classList.add('showtime-item', 'py-1', 'px-3', 'border', 'rounded', 'mb-1', 'cursor-pointer');
                div.textContent = time;

                div.dataset.showId = show.show_id;
                div.dataset.showDate = show.date;
                div.dataset.showTime = show.time;
                div.dataset.formattedTime = time;

                div.addEventListener('click', () => {
                    selectedShowtime = {
                        id: div.dataset.showId,
                        date: div.dataset.showDate,
                        time: div.dataset.showTime,
                        formattedTime: div.dataset.formattedTime
                    };
                });
                a.appendChild(div);
                showtimesList.appendChild(a);
            });
        }

        // Check if device is touch-enabled
        function checkTouchDevice() {
            isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        }

        // Desktop hover events (only active on non-touch devices)
        dateSelector.addEventListener('mouseenter', function() {
            if (!isTouchDevice) {
                showDateExpandedView();
            }
        });

        dateSelector.addEventListener('mouseleave', function() {
            if (!isTouchDevice) {
                showDateCollapsedView();
            }
        });

        // Click event for touch devices (mobile and tablet)
        dateCollapsed.addEventListener('click', function() {
            if (isTouchDevice) {
                toggleDateView();
            }
        });

        // Close expanded view when clicking outside (touch devices)
        document.addEventListener('click', function(event) {
            if (isTouchDevice && isExpanded && !dateSelector.contains(event.target)) {
                showDateCollapsedView();
            }
        });

        // Date item click events
        dateItems.forEach((item, index) => {
            item.addEventListener('click', function (event) {
                event.stopPropagation(); // Prevent event bubbling
                selectedDateIndex = index;
                updateSelectedDate();
                showDateCollapsedView();
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            checkTouchDevice();
            if (!isTouchDevice && isExpanded) {
                // Reset to collapsed view when switching to desktop
                showDateCollapsedView();
            }
        });

        // Initialize
        updateSelectedDate();
        showDateCollapsedView();
        checkTouchDevice();
    });
</script>


@endsection