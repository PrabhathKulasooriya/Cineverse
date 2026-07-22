@extends('customer_include.main')
@section('pageSpecificStyles')
<link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
<link rel="stylesheet" href="{{ asset('css/slider.css') }}">
@endsection

@section('pageSpecificContent')

    <div id="video">
        <div class="preloader">
            <div class="preloader-bounce">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div id="fullpage" class="fullpage-default">

            <div class="section animated-row home-section" data-section="slide01">
                <div class="section-inner section-slider">

                    <div class="slider-container2">
                    @foreach($imageSlider as $image)
                        <a href="{{ route('bookmovie', ['movie_id' => $image->movies_movie_id]) }}">
                        <img src="{{ URL::asset('sliderImages/' . $image['image']) }}" alt="Slider Image" class="slider-image-background">                 
                        </a>
                    @endforeach
                     </div>

                    <div class="slider-container">
                        @foreach($imageSlider as $image)
                            <a href="{{ route('bookmovie', ['movie_id' => $image->movies_movie_id]) }}">
                                <img src="{{ URL::asset('sliderImages/' . $image['image']) }}" alt="Slider Image" class="slider-image">                 
                            </a>
                        @endforeach
                     </div>

                     <button id="prevBtn" class="image-slider-btn" style="left: 15px">
                            <i class="fa fa-chevron-left fa-3x" ></i>
                    </button>

                     <button id="nextBtn" class="image-slider-btn" style="right: 15px">
                            <i class="fa fa-chevron-right fa-3x" ></i>
                    </button>
                    
                     <div class="scroll-down next-section animate "><span>Scroll Down</span></div>
                </div>
            </div>


            <div class="section animated-row" data-section="slide02">
            <div class="section-inner movies-section">
                <div class="row justify-content-center">
                    <div class="col-md-8 wide-col-laptop">

                        <div class="title-block animate" data-animate="fadeInUp">
                            <span>MOVIES</span>
                            <h2><i class="fa fa-video-camera" aria-hidden="true"></i>  Now Showing</h2>
                        </div>

                        <div class="services-section">
                            <div class="services-list owl-carousel ">
                                @foreach ($movies as $movie )
                            
                                <div class="animate movie-card-list" data-animate="fadeInUp">
                                    <div class="movie-card">
                                        <img src="{{URL::asset('movieImages/'.$movie->image) }}" alt="" style='width:250px' class="movie-card-image">
                                        <div class="movie-card-details">
                                            <p class="movie-card-title">{{$movie->name}}</p>
                                            <p>{{$movie->language}}</p>
                                            <a href="{{ route('bookmovie', ['movie_id' => $movie->movie_id]) }}" class="btn btn-primary">Book Now</a>
    
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>



        <div class="section animated-row" data-section="slide03">
            <div class="section-inner">
                <div class="about-section">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 wide-col-laptop">
                            <div class="row">
                                <div class="col-md-8 mx-auto text-center">
                                    <div class="about-contentbox">

                                        <div class="animate" data-animate="fadeInUp">
                                            <span>About Us</span>
                                            <h2>Who we are?</h2>
                                            <p>We provide a modern cinema experience with state-of-the-art screens, comfortable seating, and a diverse selection of films. Our focus is on delivering immersive entertainment and excellent service for all movie lovers.

</p>
                                        </div>

                                        <div class="facts-list owl-carousel">

                                            <div class="item animate" data-animate="fadeInUp">
                                                <div class="counter-box">
                                                    <i class="fa fa-video-camera counter-icon" aria-hidden="true"></i><span class="count-number">4k</span> Ultra HD
                                                </div>
                                            </div>

                                            <div class="item animate" data-animate="fadeInUp">
                                                <div class="counter-box">
                                                    <i class="fa fa-smile-o counter-icon" aria-hidden="true"></i><span class="count-number">5<i class="fa fa-star" aria-hidden="true"></i></span> Customer Satisfaction
                                                </div>
                                            </div>

                                            <div class="item animate" data-animate="fadeInUp">
                                                <div class="counter-box">
                                                    <i class="fa fa-ticket counter-icon" aria-hidden="true"></i><span class="count-number">500000+</span> Tickets Sold
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="section animated-row" data-section="slide04">
            <div class="section-inner section-contact">
                <div class="row justify-content-center">
                    <div class="col-md-7 wide-col-laptop">
                        <div class="title-block animate" data-animate="fadeInUp">
                            <span>Contact</span>
                            <h2>Get In Touch!</h2>
                        </div>
                        <div class="contact-section">
                            <div class="row">
                                <div class="col-md-12 animate" data-animate="fadeInUp">
                                    <div class="contact-box">
                                        <div class="contact-row contact-row-location">
                                            <i class="fa fa-map-marker"></i> 123 New Street , Kuliyapitiya  60200
                                            <div class="contact-box-map offline-map-placeholder" style=" display:flex; align-items:center; justify-content:center; background:#f8f9fa; color:#333; padding:16px; border-radius:8px;">
                                                Offline map unavailable. 
                                            </div>
                                        </div>
                                        <div class="contact-row">
                                            <i class="fa fa-phone"></i> 0115 123 456
                                        </div>
                                        <div >
                                            <a href="mailto:info@cineverse.com" class="contact-row">
                                            <i class="fa fa-envelope"></i> info@cineverse.com
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    </div>


@endsection

@section('pageSpecificScript')


{{-- Slider Scripts --}}
<script>

setTimeout(() => $(".alert").fadeOut("slow", function() { $(this).remove(); }), 3000);
    
window.addEventListener('load', function () {
    const sliderContainer = document.querySelector('.slider-container');
    const sliderContainer2 = document.querySelector('.slider-container2');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    // Array to store slider information
    const sliders = [];
    
    // Function to initialize a slider
    function initializeSlider(container) {
        if (!container) return null; // Exit if container doesn't exist
        
        // Create a new slider container
        const slider = document.createElement('div');
        slider.classList.add('slider');
        
        // Get all link elements that contain images
        const linkElements = container.querySelectorAll('a');
        const slideCount = linkElements.length;
        
        if (slideCount === 0) return null;
        
        // Clone the link elements (which contain the images)
        linkElements.forEach(link => {
            const clone = link.cloneNode(true);
            const img = clone.querySelector('img');
            if (img) {
                img.style.width = '100%';
                img.style.height = 'calc(100vh - 80px)';
                img.style.objectFit = 'fill'; 
            }
            
            clone.style.width = `${100 / slideCount}%`;
            clone.style.flexShrink = '0';
            slider.appendChild(clone);
        });
        
        slider.style.width = `${slideCount * 100}%`;
        slider.style.display = 'flex';
        slider.style.transition = 'transform 0.8s ease-in-out';
        
        container.innerHTML = '';
        container.appendChild(slider);
        
        return {
            element: slider,
            slideCount: slideCount,
            currentIndex: 0
        };
    }
    
    // Initialize both sliders
    const slider1 = initializeSlider(sliderContainer);
    const slider2 = initializeSlider(sliderContainer2);
    
    // Add valid sliders to our array
    if (slider1) sliders.push(slider1);
    if (slider2) sliders.push(slider2);
    
    function goToSlide(index) {
        sliders.forEach(slider => {
            let newIndex = index;
            if (newIndex < 0) {
                newIndex = slider.slideCount - 1;
            } else if (newIndex >= slider.slideCount) {
                newIndex = 0;
            }
            
            slider.element.style.transform = `translateX(-${newIndex * (100 / slider.slideCount)}%)`;
            slider.currentIndex = newIndex;
        });
    }
    
    // Function to handle auto sliding
    let intervalId;
    function startAutoSlide() {
        clearInterval(intervalId);
        intervalId = setInterval(() => {

            if (sliders.length > 0) {
                goToSlide(sliders[0].currentIndex + 1);
            }
        }, 3000);
    }
    
    if (sliders.length > 0) {
        startAutoSlide();
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (sliders.length > 0) {
                goToSlide(sliders[0].currentIndex - 1);
                startAutoSlide(); // reset timer
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (sliders.length > 0) {
                goToSlide(sliders[0].currentIndex + 1);
                startAutoSlide(); // reset timer
            }
        });
    }
});
</script>
{{-- Slider Scripts End --}}



</script>
@endsection

