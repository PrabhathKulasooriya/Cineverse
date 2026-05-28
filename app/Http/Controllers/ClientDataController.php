<?php


namespace App\Http\Controllers;


use App\Bookings;
use App\BookedSeats;
use App\Movies;
use App\Shows;
use App\User;
use Illuminate\Http\Request;
class ClientDataController extends Controller

{

    public function upcomingBookings(Request $request){
        $userID = auth()->user()->idmaster_user;
        $bookings = Bookings::where('master_user_idmaster_user',$userID)
                    ->where('payment_status', 'PAID')
                    ->whereHas('show', function($query) {
                        $query->whereRaw(
                            "STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?", [now()] );
                    })
                    ->get();
        $allBookingsData = [];
                if($bookings->isNotEmpty()) {
                foreach($bookings as $booking) {
                    $bookedSeats = BookedSeats::with('seat') 
                                    ->where('bookings_booking_id', $booking->booking_id)
                                    ->get();
    
                    $seats = $bookedSeats->pluck('seat'); 
                    
                    $movie = Movies::find($booking->movies_movie_id);
                    $show = Shows::find($booking->shows_show_id);
                    
                    $bookingData = $booking->toArray();
                    
                    $bookingData['movie_name'] = $movie->name;
                    $bookingData['show_time'] = $show->time;
                    $bookingData['show_date'] = $show->date;
                    $bookingData['seats'] = $seats;
                    
                    $allBookingsData[] = $bookingData;
                    }
            
            return view('customer.upcomingBookings', ['title' => 'Upcoming Bookings','bookings' => $allBookingsData ]);      
        }else{
            return view('customer.upcomingBookings', ['title' => 'Upcoming Bookings']);
        }
    }

    public function pastBookings(){$userID = auth()->user()->idmaster_user;
        $bookings = Bookings::where('master_user_idmaster_user',$userID)
                    ->whereHas('show', function($query) {
                        $query->whereRaw(
                            "STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') <= ?", [now()] );
                    })
                    ->get();
        $allBookingsData = [];
                if($bookings->isNotEmpty()) {
                foreach($bookings as $booking) {
                    $bookedSeats = BookedSeats::with('seat') 
                                    ->where('bookings_booking_id', $booking->booking_id)
                                    ->get();
    
                    $seats = $bookedSeats->pluck('seat'); 
                    
                    $movie = Movies::find($booking->movies_movie_id);
                    $show = Shows::find($booking->shows_show_id);
                    
                    $bookingData = $booking->toArray();
                    
                    $bookingData['movie_name'] = $movie->name;
                    $bookingData['show_time'] = $show->time;
                    $bookingData['show_date'] = $show->date;
                    $bookingData['seats'] = $seats;
                    
                    $allBookingsData[] = $bookingData;
                    }
            
            return view('customer.pastBookings', ['title' => 'Ticket Verification','bookings' => $allBookingsData ]);      
        }else{
            return view('customer.pastBookings', ['title' => 'Ticket Verification']);
        }}

        public function pendingPayments()
        {
            
            $pendingBookings = Bookings::where('payment_status', 'PENDING')
                                        ->where('master_user_idmaster_user', auth()->user()->idmaster_user)->get();
    
            
            $pendingPaymentsData = [];
    
            foreach ($pendingBookings as $booking) {
               
                $movie = Movies::find($booking->movies_movie_id);
                $show = Shows::find($booking->shows_show_id);     
                $selectedSeatsId = BookedSeats::where('bookings_booking_id', $booking->booking_id)->pluck('seats_seat_id')->toArray();            
                
                $pendingPaymentsData[] = [
                    'booking_id' => $booking->booking_id,
                    'movie_id' => $movie->movie_id,
                    'show_id' => $show->show_id,
                    'movie' => $movie->name,
                    'date' => $show->date,
                    'time' => $show->time,
                    'amount'=>  $booking->amount,
                    'payment_status' => $booking->payment_status,
                    'selectedSeatsId' => $selectedSeatsId,
                ];
            }
    
            
            return view('customer.pendingPayment', [
                'title' => 'Pending Payments',
                'pendingPayments' => $pendingPaymentsData,
            ]);
        }


        public function payment(Request $request){
                if($request->bookingData == null){
                    return redirect()->route('customerPendingPayments')->with('error', 'Booking data not found!');
                }
                $bookingData = json_decode($request->bookingData, true);
                
                $booking = Bookings::where('booking_id', $bookingData['booking_id'])->first();
                
                if($booking == null || !$booking){
                    return redirect()->route('customerPendingPayments')->with('error', 'Booking data not found!');
                }
                if($booking->payment_status == 'PAID'){
                    return redirect()->route('customerPendingPayments')->with('error', 'Booking already paid!');
                }
                if($booking){
                    session(['manual_booking _data'=>$bookingData]);
                    return redirect()->route('paymentpage');
                }
                
        }

        public function cancelPendingPayments(Request $request){
            $bookingId = $request->bookingData;
        if($bookingId == null){
            return redirect()->back()->with('error', 'Booking ID not found.');
        }
        $booking = Bookings::find($bookingId);
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }
        if($booking->payment_status == 'PAID'){
            return redirect()->back()->with('error', 'Booking already paid.');
        }
        else if($booking->payment_status == 'PENDING'){
            BookedSeats::where('bookings_booking_id', $bookingId)->delete();
            $booking->delete();
            
            return redirect()->back()->with('success', 'Booking canceled successfully.');}
        }
        
}