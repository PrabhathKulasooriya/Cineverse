<?php


namespace App\Http\Controllers;


use App\Bookings;
use App\BookedSeats;
use App\Movies;
use App\Shows;
use App\User;
use Exception;
use Illuminate\Support\Facades\Log;
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

    public function pastBookings()
    {
        $userID = auth()->user()->idmaster_user;

        $bookings = Bookings::with(['movie', 'show', 'bookedSeats.seat'])
            ->where('master_user_idmaster_user', $userID)
            ->whereHas('show', function($query) {
                $query->whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') <= ?", [now()]);
            })
            ->get(); 
            
        $allBookingsData = [];

        if ($bookings->isNotEmpty()) {
            foreach($bookings as $booking) {
                $bookingData = $booking->toArray();
                
                $bookingData['movie_name'] = $booking->movie->name ?? null;
                $bookingData['show_time']  = $booking->show->time ?? null;
                $bookingData['show_date']  = $booking->show->date ?? null;
                
                $bookingData['seats'] = $booking->bookedSeats->pluck('seat');
                
                $allBookingsData[] = $bookingData;
            }
        }

        return view('customer.pastBookings', [
            'title' => 'Past Bookings',
            'bookings' => $allBookingsData
        ]);
    }

    public function pendingPayments()
    {
        $this->cleanupExpiredBookings();
        $pendingBookings = Bookings::where('payment_status', 'PENDING')
                                    ->where('master_user_idmaster_user', auth()->user()->idmaster_user)->get();

        $pendingPaymentsData = [];

        $lastMinuteWindow = env('BOOKING_LAST_MINUTE_WINDOW', 60);
        $shortExpire = env('BOOKING_EXPIRATION_MINUTES_SHORT', 5);
        $standardExpire = env('BOOKING_EXPIRATION_MINUTES', 15);

        foreach ($pendingBookings as $booking) {
            $movie = Movies::find($booking->movies_movie_id);
            $show = Shows::find($booking->shows_show_id);     
            $selectedSeatsId = BookedSeats::where('bookings_booking_id', $booking->booking_id)->pluck('seats_seat_id')->toArray();            
            
            // Calculate Expiration Time
            $bookingTime = \Carbon\Carbon::parse($booking->created_at);
            $showDateTime = \Carbon\Carbon::parse($show->date . ' ' . $show->time);

            $minutesUntilShow = now()->diffInMinutes($showDateTime, false);
            $expireMinutes = ($minutesUntilShow <= $lastMinuteWindow) ? $shortExpire : $standardExpire;

            $expiresAt = $bookingTime->copy()->addMinutes($expireMinutes);

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
                'expires_at' => $expiresAt->toIso8601String()
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

        //Cleanup Expired bookings*************************************************************************
        private function cleanupExpiredBookings()
        {
            try {
                $possibleCleanups = Bookings::where('payment_status', 'PENDING')->get();

                $lastMinuteWindow = env('BOOKING_LAST_MINUTE_WINDOW', 60);
                $shortExpire = env('BOOKING_EXPIRATION_MINUTES_SHORT', 5);
                $standardExpire = env('BOOKING_EXPIRATION_MINUTES', 15);

                foreach ($possibleCleanups as $booking) {
                    $show = Shows::find($booking->shows_show_id);
                    if (!$show) {
                        continue;
                    }

                    $bookingTime = \Carbon\Carbon::parse($booking->created_at);
                    $showDateTime = \Carbon\Carbon::parse($show->date . ' ' . $show->time);
                    
                    $minutesUntilShow = now()->diffInMinutes($showDateTime, false);

                    if ($minutesUntilShow <= $lastMinuteWindow) {
                        $expireMinutes = $shortExpire;
                    } else {
                        $expireMinutes = $standardExpire;
                    }

                    $cutoffTime = now()->subMinutes($expireMinutes);

                    if ($bookingTime->lessThan($cutoffTime)) {
                        BookedSeats::where('bookings_booking_id', $booking->booking_id)->delete();
                        $booking->delete();
                        Log::info('Cleaned up expired booking: ' . $booking->booking_id);
                    }
                }

                return response()->json(['message' => 'Expired bookings cleaned up successfully']);
            } catch (Exception $e) {
                Log::error('Error cleaning up expired bookings: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to cleanup expired bookings'], 500);
            }
        }
        
}