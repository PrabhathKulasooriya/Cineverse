<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Movies;
use App\Shows;
use App\Seats;
use App\SeatType;
use App\BookedSeats;
use App\Bookings;
use Exception;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;





class BookingController extends Controller
{

    //Movie and Seat Selection ****************************************************************************************************
    public function index($movie_id)
    {
        $shows = Shows::where('movies_movie_id', $movie_id)
                ->whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?", [now()->subHours(1)])
                ->get();

        $movie = Movies::find($movie_id);

        if(!$movie){
            return redirect()->route('home');
        }
        
        $movie->formatted_duration = $this->formatDuration($movie->duration);
        
        if ($movie->status == 1){

        return view('bookings.moviePage', compact('movie', 'shows'));
        
        }else{
            return redirect()->route('home');
        }
    }


    public function seatSelection($show){

        $this->cleanupExpiredBookings();

        $show = Shows::find($show);
        $movie = Movies::find($show->movies_movie_id);
        $seats = Seats::orderBy('row', 'asc')->orderBy('number', 'asc')->get();
        $showId = $show->show_id;
        $prime_price = SeatType::where('type', 'PRIME')->first()->price;
        $standard_price = SeatType::where('type', 'STANDARD')->first()->price;

        $bookedSeatIds = BookedSeats::whereHas('booking', function ($query) use ($showId) {
                            $query->where('shows_show_id', $showId);
                            })->pluck('seats_seat_id')->toArray();
        
        return view('bookings.seatSelection', 
            compact('show', 'movie', 'seats','prime_price','standard_price','bookedSeatIds'));
    }

    public function confirmSeatSelection(Request $request){

        $selectedSeatsId = $request->selectedSeatsId;

        if (is_string($selectedSeatsId)) {
            $selectedSeatsId = json_decode($selectedSeatsId, true);
        }

        //Check seat availability
        $showId = $request->showId;
        $bookedSeatIds = BookedSeats::whereHas('booking', function ($query) use ($showId) {
                            $query->where('shows_show_id', $showId);
                            })->pluck('seats_seat_id')->toArray();

        $conflictSeats = array_intersect($selectedSeatsId, $bookedSeatIds);

        if(!empty($conflictSeats)){
            return redirect()->back()->with('error', 'Selected seats are already booked. Please select another seat.');
        }

        // Get customer data
        if (auth()->check()) {
            $customerId = auth()->user()->idmaster_user;
            
        } else {
            $customerId = null;
            
        }

        try {
            // Validate seats
            if (!is_array($selectedSeatsId) || empty($selectedSeatsId)) {
                throw new \Exception('Invalid seat selection data');
            }

            // Generate unique booking ID
            $showId = (int) $request->showId;
            $firstSeatId = (int) (is_array($selectedSeatsId) ? $selectedSeatsId[0] : $selectedSeatsId);
            $timeComponent = (int) substr(microtime(true) * 10000, -4);

            $bookingId = (int) (
                str_pad($showId, 2, '0', STR_PAD_LEFT) .
                str_pad($firstSeatId, 3, '0', STR_PAD_LEFT) .
                $timeComponent
            );

            // Save booking with PAID status
            $saveBooking = new Bookings();
            $saveBooking->booking_id = $bookingId;
            $saveBooking->shows_show_id = $showId;
            $saveBooking->movies_movie_id = $request->movieId;
            $saveBooking->master_user_idmaster_user = $customerId;
            $saveBooking->amount = $request->totalAmount;
            $saveBooking->payment_status = 'PENDING'; 
            $saveBooking->save();

            foreach ($selectedSeatsId as $seatId) {
                $seatIdInt = (int) $seatId;

                if ($seatIdInt <= 0) {
                    \Log::warning('Skipping invalid seat ID: ' . $seatId);
                    continue;
                }

                $saveSeats = new BookedSeats();
                $saveSeats->bookings_booking_id = $bookingId;
                $saveSeats->seats_seat_id = $seatIdInt;
                $saveSeats->save();
            }

            // Save booking data to session 
            $bookingData = [
                'amount' => $request->totalAmount,
                'selectedSeatsId' => $selectedSeatsId,
                'movieId' => $request->movieId,
                'showId' => $request->showId,
                'booking_id' => $bookingId,
            ];
            session(['manual_booking_data' => $bookingData]);

            return redirect()->route('paymentpage');
            } catch (\Exception $e) {
            \Log::error('Seat Selection error: ' . $e->getMessage());
            return redirect()->route('seatSelection')->with('error', 'Seat selection failed.. Please try again.');
            }
    }



    //Ticket Page ********************************************************************************
   public function ticketPage(Request $request){
    // Get the completed booking from session
    $booking = session('completed_booking');
    if ($booking) {
        
        $movie = Movies::find($booking['movieId']);
        $show = Shows::find($booking['showId']);
        
        
        $booking['movie_name'] = $movie->name;
        $booking['show_time'] = $show->time;
        $booking['show_date'] = $show->date;

        $seats = Seats::whereIn('seat_id', $booking['selectedSeatsId'])
                ->orderBy('seat_id', 'asc')
                ->get();
        $booking['seats'] = $seats;

        $qr = QrCode::create($booking['booking_id']);
        $writer = new PngWriter();
        $result = $writer->write($qr);
        $qrCodePngBase64 = base64_encode($result->getString());
        
        try {
            Mail::to($booking['customer_email'])->send(new TicketMail($booking, $seats, $qrCodePngBase64, 'PNG'));
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
            session()->flash('error', 'Booking successful, but we couldn’t send the email.');
            return view('bookings.ticketPage', compact('booking'));
        }


        session()->flash('success', 'Payment successful! Ticket sent to your email.');
        return view('bookings.ticketPage', compact('booking'));
    }
    
    // If no booking found, show empty ticket page
    return view('bookings.ticketPage');
    }


    // Duration conversion methods******************************************************************
    protected function formatDuration($minutes){
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        $formattedDuration = '';
    
        if ($hours > 0) {
         $formattedDuration .= $hours . 'h ';
     }
    
        if ($remainingMinutes > 0) {
         $formattedDuration .= $remainingMinutes . 'm';
     }

        return trim($formattedDuration);
    }

   

    //Cleanup Expired bookings*************************************************************************
    private function cleanupExpiredBookings(){
        try {

            $cutoffTime = now()->subMinutes(20);
            
            $expiredBookings = Bookings::where('payment_status', 'PENDING')
                ->where('created_at', '<', $cutoffTime)
                ->get();
    
            foreach ($expiredBookings as $booking) {
                
                BookedSeats::where('bookings_booking_id', $booking->booking_id)->delete();
                $booking->delete();
                \Log::info('Cleaned up expired booking: ' . $booking->booking_id);
            }
            
            if ($expiredBookings->count() > 0) {
                \Log::info('Cleanup completed: ' . $expiredBookings->count() . ' expired bookings removed');
            }
        } catch (\Exception $e) {
            \Log::error('Error cleaning up expired bookings: ' . $e->getMessage());
        }
    }

}
