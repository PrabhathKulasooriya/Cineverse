<?php

use App\Client;

Auth::routes();




//Client Interface
Route::get('/', 'ClientInterfaceController@index')->name('home');




Route::get('/signin', function () {

        if (!session()->has('url.intended')) {
        session(['url.intended' => url()->previous()]);
        }
        
        return view('signin');
        })->middleware('guest')->name('signin');


//Login
Route::post('/signin', 'SecurityController@login')->name('login');



//Client Sign Up
Route::get('/clientSignup', 'SecurityController@signup')->middleware('guest')->name('clientSignup');
Route::post('/saveClient', 'ClientController@saveClient')->middleware('guest')->name('saveClient');



//Booking Routes
Route::get('/bookmovie/{id}', 'BookingController@index')->name('bookmovie');
Route::get('/seatSelection/{id}', 'BookingController@seatSelection')->name('seatSelection');
Route::post('/confirmSeatSelection', 'BookingController@confirmSeatSelection')->name('confirmSeatSelection');

Route::get('/paymentPage', 'PaymentController@paymentPage')->name('paymentpage');
Route::post('/manualPayment', 'PaymentController@manualPayment')->name('manualPayment');

Route::post('/stripePayment', 'PaymentController@stripePayment')->name('stripePayment');
Route::get('/success', 'PaymentController@success')->name('success');
Route::get('/cancel', 'PaymentController@cancel')->name('cancel');
Route::get('/cleanup-expired-bookings', ['PaymentController@cleanupExpiredBookings'])->name('cleanup.expired');

Route::get('/ticketpage', 'BookingController@ticketPage')->name('ticketpage');
Route::post('/sendTicketEmail', 'TicketController@sendTicketEmail')->name('sendTicketEmail');
Route::get('/downloadTicket/{booking_id}', 'TicketController@downloadTicket')->name('downloadTicket');
Route::get('/printTicket/{booking_id}', 'TicketController@printTicket')->name('printTicket');

Route::post('/stripePayment2', 'BookingController@stripePayment2')->name('stripePayment2');


//Search Movies
Route::get('/search', 'MoviesController@search')->name('search');



//After Login ('auth')**************************************************************************************************************************
Route::group(['middleware' => 'auth', 'prefix' => ''], function () {


    //Log Out
    Route::get('/logout', 'SecurityController@logoutNow')->name('logout');


    //Status Change
    Route::post('/activateDeactivate', 'StatusController@activateDeactivate')->name('activateDeactivate');

    //My Account
    Route::get('/myAccount', 'MyAccountController@index')->name('myAccount');
    Route::post('/getUserDetails', 'MyAccountController@getUserDetails')->name('getUserDetails');
    Route::post('/updateUserDetails', 'MyAccountController@updateUserDetails')->name('updateUserDetails');
    Route::post('/changePassword', 'MyAccountController@changePassword')->name('changePassword');

    //Ticket and Payments
    Route::post('/verifyTicket', 'TicketController@verifyTicket')->name('verifyTicket');
    Route::get('/ticketVerification', 'TicketController@ticketVerification')->name('ticketVerification');
    Route::post('/cancelPayment', 'PaymentController@cancelPayment')->name('cancelPayment');

    Route::post('/payPending','ClientDataController@payment')->name('payPending');  

    //Routes accessible to Admin, Management Employees, and Counter Employees***************************************************************************
    Route::group(['middleware' => 'role:1,2,3'], function () {

        //Dashboard
        Route::get('/dashboard', 'DashboardController@dashboardIndex')->name('dashboard');

        //Upcoming Shows
        Route::get('/shows','ShowsController@index')->name('shows');

        //Movies
        Route::get('/movies', 'MoviesController@index')->name('movies');

        //Bookings
        Route::get('/pendingPayments', 'PaymentController@pendingPayments')->name('pendingPayments');
        Route::get('/findBooking', 'TicketController@findBooking')->name('findBooking');
        Route::post('/findBooking', 'TicketController@findBookingcheck')->name('findBookingCheck');

        //Client Management In Admin
        Route::get('/clientManagement', 'ClientController@index')->name('clientManagement');
        Route::post('/saveClientByAdmin', 'ClientController@saveClientByAdmin')->name('saveClientByAdmin');
        Route::post('/updateClient', 'ClientController@updateClient')->name('updateClient');
        
    });




    //Routes accessible to Admin*************************************************************************************************************************
    Route::group(['middleware' => 'role:1'], function () {
    
        //User Management In Admin
        Route::get('/employeeManagement', 'EmployeeController@index')->name('employeeManagement');
        Route::post('/saveEmployee', 'EmployeeController@saveUser')->name('saveEmployee');
        Route::post('/updateEmployee', 'EmployeeController@updateUser')->name('updateEmployee');

        //ShowTimes
        Route::get('/showtimes', 'ShowtimesController@index')->name('showtimes');
        Route::post('/saveShowtime', 'ShowtimesController@saveShowtime')->name('saveShowtime');
        Route::put('/updateShowtime', 'ShowtimesController@updateShowtime')->name('updateShowtime');
        Route::delete('/destroyShowtime/{id}', 'ShowtimesController@destroyShowtime')->name('destroyShowtime');

        //Ticket Prices
        Route::get('/ticketSettings', 'TicketController@ticketPrices')->name('ticketPrices');
        Route::put('/updateTicketPrice', 'TicketController@updateTicketPrice')->name('updateTicketPrice');

        //Screened Movies
        Route::get('/screenedMovies', 'MoviesController@screenedMovies')->name('screenedMovies');
        Route::put('/activateScreenMovie', 'MoviesController@activateScreenMovie')->name('activateScreenMovie');

        //Screened Shows
        Route::get('/screenedShows','ShowsController@screened')->name('screenedShows');

        //Reports
        Route::get('/revenueReport', 'ReportController@revenueReport')->name('revenueReport');
        Route::get('/clientReport', 'ReportController@clientReport')->name('clientReport');

    });

    //Routes accessible to Admin and Management Employees***********************************************************************************
    Route::group(['middleware' => 'role:1,2,'], function () {

        //Movies
        Route::post('/movies', 'MoviesController@store')->name('saveMovie');
        Route::put('/movies', 'MoviesController@update')->name('updateMovie');
        Route::delete('/destroyMovie','MoviesController@destroy')->name('destroyMovie');
        

        //Movie Slider
        Route::get('/movieSlider', 'SliderController@index')->name('movieSlider');
        Route::post('/movieSlider', 'SliderController@store')->name('saveSliderImage');
        Route::put('/movieSlider/{id}', 'SliderController@update')->name('updateSliderImage');
        Route::delete('/movieSlider/{id}', 'SliderController@destroy')->name('destroySliderImage');

        //Shows
        Route::post('/getAvailableShowtimes', 'ShowsController@getAvailableShowtimes')->name('getAvailableShowtimes');
        Route::post('/shows','ShowsController@store')->name('saveShow');
        Route::post('/updateShow','ShowsController@update')->name('updateShow');
        Route::post('/destroyShow','ShowsController@destroy')->name('destroyShow');
        

    
    });


    //Routes accessible to Admin and Ticket Counter Employees***********************************************************************************

    Route::group(['middleware' => 'role:1,3'], function () {

        //Ticket Verification
        


    });

    
    //Routes accessible only user
    Route::group(['middleware' => 'role:4'], function () {

    Route::get('/upcomingBookings', 'ClientDataController@upcomingBookings')->name('upcomingBookings');
    Route::get('/pastBookings', 'ClientDataController@pastBookings')->name('pastBookings');
    Route::get('/customerPendingPayments', 'ClientDataController@pendingPayments')->name('customerPendingPayments');

    });


    

});

