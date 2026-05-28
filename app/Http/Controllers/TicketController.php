<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeatType;
use Illuminate\Support\Facades\Validator;
use App\Bookings;
use App\BookedSeats;
use App\Movies;
use App\Shows;
use App\Seats;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;
use PDF;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;


class TicketController extends Controller
{
    public function ticketPrices(){

        $seatTypes = SeatType::all();   

        return view('management.ticketSettings',compact('seatTypes'),['title' => 'Ticket Prices for Seat Types']);
    }   

    public function updateTicketPrice(Request $request){
        $seatType = SeatType::find($request->hiddenSeatTypeId);
        $seatType->price = $request->price;
        $seatType->save();
        return redirect()->route('ticketPrices');
    }


    //Ticket Verification *****************************************************************************************************
    public function ticketVerification(){

        return view('management.ticketVerification', [
            'title' => 'Ticket Verification',
            'booking' => session('booking'),
            'seats' => session('seats')
        ]);   
    }
      

    public function verifyTicket(Request $request){
        $validator = Validator::make($request->all(), [
            'bookingId' => 'required|numeric',
        ],[
            'bookingId.required' => 'Ticket ID is required',
            'bookingId.numeric' => 'Ticket ID must be numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticketVerification')->with('error', $validator->errors()->first());
                
        }

        try{
            $booking = Bookings::where('booking_id', $request->bookingId)->first();
            
            if($booking){

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
                
                return redirect()->route('ticketVerification')->with([
                    'success' => 'Ticket verified successfully!',
                    'booking' => $bookingData,
                    'seats' => $seats
                ]);

            } else {
                return redirect()->route('ticketVerification')->with('error', 'Ticket is not found!');
            }
        } catch (\Exception $e) {
            return redirect()->route('ticketVerification')->with('error', 'Ticket is not found!');
        }
    }

 //Find Booking *****************************************************************************************************
    public function findBooking(){

        return view('management.findBooking', [
            'title' => 'Find Booking',
            'bookings' => session('bookings'),
        ]);
    }

    public function findBookingcheck(Request $request){

        $validator=Validator::make($request->all(), [
            'bookingEmail' => 'required|email',
        ],[
            'bookingEmail.required' => 'Email is required',
            'bookingEmail.email' => 'Enter valid email',
        ]);
        if ($validator->fails()) {
            return redirect()->route('findBooking')->with('error', $validator->errors()->first());
        }
        
        try {
        $bookings = Bookings::where('email', $request->bookingEmail)
                    ->whereHas('show', function($query) {
                        $query->whereRaw(
                            "STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') >= ?", [now()] );
                    })
                    ->get();

        
        if($bookings->isNotEmpty()) {
            
            $allBookingsData = [];
            
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
        return redirect()->route('findBooking')->with([
            'success' => 'Bookings found successfully!',
            'bookings' => $allBookingsData, 
        ]);
        
        } else {
                return redirect()->route('findBooking')->with('error', 'No bookings found for this email!');
            }
        } catch (\Exception $e) {
            return redirect()->route('findBooking')->with('error', 'An error occurred while verifying tickets!');
        }

    }

    //Send Ticket via Email******************************************************************************
    public function sendTicketEmail(Request $request){
        $booking_id = $request->booking_id;

        if(!$booking_id){
            return redirect()->back()->with('error', 'Booking ID is required');
        }

        $booking = Bookings::where('booking_id', $booking_id)->first();
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found');
        }

        $movie = Movies::find($booking->movies_movie_id);
        $show = Shows::find($booking->shows_show_id);

        if (!$movie || !$show) {
            return redirect()->back()->with('error', 'Movie or show not found');
        }

        $seat_ids = BookedSeats::where('bookings_booking_id', $booking_id)->get();
        $seats = Seats::whereIn('seat_id', $seat_ids->pluck('seats_seat_id')->toArray())->get();
        
        $booking['movie_name'] = $movie->name;
        $booking['show_time'] = $show->time;
        $booking['show_date'] = $show->date;

        $qr = QrCode::create($booking['booking_id']);
        $writer = new PngWriter();
        $result = $writer->write($qr);
        $qrCodePngBase64 = base64_encode($result->getString());
    
        $email = $booking->email;
        if (!$email) {
            return redirect()->back()->with('error', 'No email address associated with this booking');
        }

        Mail::to($email)->send(new TicketMail($booking->toArray(), $seats, $qrCodePngBase64, 'PNG'));
        return redirect()->route('ticketVerification')->with([
            'success' => 'Ticket successfully sent to '.$email.'!',
            'booking' => $booking->toArray(),
            'seats' => $seats
        ]);

    }

    public function downloadTicket($booking_id){
        if(!$booking_id){
            session()->flash('error', 'No booking id found!');
            return back();
        }

        $booking = Bookings::where('booking_id', $booking_id)->first();
        if(!$booking){
            session()->flash('error', 'No such booking found!');
            return back();
        }
        try{
            $movie = Movies::find($booking->movies_movie_id);
            $show = Shows::find($booking->shows_show_id);

            if (!$movie || !$show) {
                return redirect()->back()->with('error', 'Movie or show not found');
            }

            $seat_ids = BookedSeats::where('bookings_booking_id', $booking_id)->get();
            $seats = Seats::whereIn('seat_id', $seat_ids->pluck('seats_seat_id')->toArray())->get();
            
            $booking['movie_name'] = $movie->name;
            $booking['show_time'] = $show->time;
            $booking['show_date'] = $show->date;

            $qr = QrCode::create($booking['booking_id']);
            $writer = new PngWriter();
            $result = $writer->write($qr);
            $qrCodePngBase64 = base64_encode($result->getString());

            $ticketMail = new TicketMail($booking->toArray(), $seats, $qrCodePngBase64, 'PNG');
            $mailContent = $ticketMail->render();
            $pdf = PDF::loadHTML($mailContent);
            $pdf->setPaper('A5', 'portrait');
            return $pdf->download('ticket.pdf');
            } catch (\Exception $e) {
                session()->flash('error', 'An error occurred while downloading the ticket!');
                return back();

            }
    }

    public function printTicket($booking_id){
        if(!$booking_id){
            session()->flash('error', 'No booking id found!');
            return back();
        }

        $booking = Bookings::where('booking_id', $booking_id)->first();
        if(!$booking){
            session()->flash('error', 'No such booking found!');
            return back();
        }
        try{
            $movie = Movies::find($booking->movies_movie_id);
            $show = Shows::find($booking->shows_show_id);

            if (!$movie || !$show) {
                return redirect()->back()->with('error', 'Movie or show not found');
            }

            $seat_ids = BookedSeats::where('bookings_booking_id', $booking_id)->get();
            $seats = Seats::whereIn('seat_id', $seat_ids->pluck('seats_seat_id')->toArray())->get();
            
            $booking['movie_name'] = $movie->name;
            $booking['show_time'] = $show->time;
            $booking['show_date'] = $show->date;

            $qr = QrCode::create($booking['booking_id']);
            $writer = new PngWriter();
            $result = $writer->write($qr);
            $qrCodePngBase64 = base64_encode($result->getString());

            $ticketMail = new TicketMail($booking->toArray(), $seats, $qrCodePngBase64, 'PNG');
            $mailContent = $ticketMail->render();
            $pdf = PDF::loadHTML($mailContent);
            $pdf->setPaper('A5', 'portrait');
            return $pdf->stream('ticket.pdf');
            } catch (\Exception $e) {
                session()->flash('error', 'An error occurred while downloading the ticket!');
                return back();

            }
    }
    
}

