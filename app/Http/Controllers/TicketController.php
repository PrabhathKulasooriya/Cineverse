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
use App\BookingSnack;
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

        $booking = session('bookingData');
        $seats = session('seats');
        
        return view('management.ticketVerification', [
            'title' => 'Ticket Verification',
            'booking' => $booking,
            'seats' => $seats,
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

        if(session()->has('bookingData')){
             session()->forget('bookingData');
        }
        if(session()->has('seats')){
            session()->forget('seats');
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
                $snacks = BookingSnack::where('booking_id', $booking->booking_id)
                            ->with('snack')
                            ->get();

                $snacksAmount = $snacks->sum(function ($item) {
                    return $item->price * $item->quantity;
                });

                $availableSeats = $booking->total_seats - $booking->entered_count;
                
                $availableSnacks = $snacks->sum(function ($item) {
                    return $item->quantity - $item->received_quantity;
                });

                $bookingData = $booking->toArray();

                $bookingData['available_seats'] = $availableSeats;
                $bookingData['available_snacks'] = $availableSnacks;
                $bookingData['movie_name'] = $movie->name;
                $bookingData['show_time'] = $show->time;
                $bookingData['show_date'] = $show->date;
                $bookingData['booking_snacks'] = $snacks;
                $bookingData['snacks_amount'] = $snacksAmount;

                session(['bookingData' => $bookingData]);
                session(['seats' => $seats]);
                
                return redirect()->route('ticketVerification')->with([
                    'success' => 'Ticket verified successfully!',
                ]);

            } else {
                return redirect()->route('ticketVerification')->with('error', 'Booking not found!');
            }
        } catch (\Exception $e) {
            return redirect()->route('ticketVerification')->with('error', 'Booking  not found!');
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

        $bookingSnacks = BookingSnack::where('booking_id', $booking_id)
                        ->with('snack')
                        ->get();
        $snackTotal = $bookingSnacks->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $booking['movie_name'] = $movie->name;
        $booking['show_time']  = $show->time;
        $booking['show_date']  = $show->date;

        $qr = QrCode::create($booking['booking_id']);
        $writer = new PngWriter();
        $result = $writer->write($qr);
        $qrCodePngBase64 = base64_encode($result->getString());

        $email = $booking->email;
        if (!$email) {
            return redirect()->back()->with('error', 'No email address associated with this booking');
        }

        $bookingArray = $booking->toArray();
        $bookingArray['booking_snacks'] = $bookingSnacks;
        $bookingArray['grandTotal'] = $booking->amount + $snackTotal;

        Mail::to($email)->send(new TicketMail($bookingArray, $seats, $qrCodePngBase64, 'PNG'));

        return redirect()->route('ticketVerification')->with([
            'success' => 'Ticket successfully sent to ' . $email . '!',
            'booking' => $bookingArray,
            'seats'   => $seats
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

            $bookingSnacks = BookingSnack::where('booking_id', $booking_id)
                            ->with('snack')
                            ->get();
            $snackTotal = $bookingSnacks->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $booking['movie_name'] = $movie->name;
            $booking['show_time'] = $show->time;
            $booking['show_date'] = $show->date;

            $qr = QrCode::create($booking['booking_id']);
            $writer = new PngWriter();
            $result = $writer->write($qr);
            $qrCodePngBase64 = base64_encode($result->getString());

            $bookingArray = $booking->toArray();
            $bookingArray['booking_snacks'] = $bookingSnacks;
            $bookingArray['grandTotal'] = $booking->amount + $snackTotal;

            $ticketMail = new TicketMail($bookingArray, $seats, $qrCodePngBase64, 'PNG');
            $mailContent = $ticketMail->render();
            $pdf = PDF::loadHTML($mailContent);
            $pdf->setPaper('A4', 'portrait');
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

            $bookingSnacks = BookingSnack::where('booking_id', $booking_id)
                        ->with('snack')
                        ->get();
            $snackTotal = $bookingSnacks->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $booking['movie_name'] = $movie->name;
            $booking['show_time'] = $show->time;
            $booking['show_date'] = $show->date;

            $qr = QrCode::create($booking['booking_id']);
            $writer = new PngWriter();
            $result = $writer->write($qr);
            $qrCodePngBase64 = base64_encode($result->getString());

            $bookingArray = $booking->toArray();
            $bookingArray['booking_snacks'] = $bookingSnacks;
            $bookingArray['grandTotal'] = $booking->amount + $snackTotal;

            $ticketMail = new TicketMail($bookingArray, $seats, $qrCodePngBase64, 'PNG');
            $mailContent = $ticketMail->render();
            $pdf = PDF::loadHTML($mailContent);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('ticket.pdf');
            } catch (\Exception $e) {
                session()->flash('error', 'An error occurred while downloading the ticket!');
                return back();
            }
    }

//Confirm Entry *****************************************************************************************************
    public function confirmEntry(Request $request){
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|numeric',
            'confirmEntry' => 'required|numeric|min:1',
        ],[
            'booking_id.required' => 'Booking ID is required',
            'booking_id.numeric' => 'Booking ID must be numeric',
            'confirmEntry.required' => 'Confirm entry is required',
            'confirmEntry.numeric' => 'Confirm entry must be numeric',
            'confirmEntry.min' => 'Confirm entry must be at least 1',
        ]);

        if ($validator->fails()) {
            return redirect()->route('ticketVerification')->with('error', $validator->errors()->first());
        }

        try{
            $booking = Bookings::where('booking_id', $request->booking_id)->first();
            
            if($booking){
                
                $total_seats = $booking->total_seats;
                $entered_count = $booking->entered_count;
                $availableSeats = $total_seats - $entered_count;

                if($request->confirmEntry > $availableSeats){
                    return redirect()->route('ticketVerification')->with('error', 'Confirm entry exceeds available seats!');
                }
                
                $booking->entered_count += $request->confirmEntry;
                $booking->save();

                if (session()->has('bookingData')) {
                    $bookingData = session('bookingData');
                    if ($bookingData['booking_id'] == $booking->booking_id) {
                        $bookingData['entered_count'] = $booking->entered_count;
                        $bookingData['available_seats'] = $booking->total_seats - $booking->entered_count;
                        session(['bookingData' => $bookingData]);
                    }
                }

                return response()->json(['success' => 'Entry confirmed successfully!']);
               
            } else {
                return redirect()->route('ticketVerification')->with('error', 'Booking not found!');
            }
        } catch (\Exception $e) {
            return redirect()->route('ticketVerification')->with('error', 'An error occurred while confirming entry!');
        }
    }

//Confirm Snack *****************************************************************************************************
    public function confirmSnack(Request $request)
    {
        $items = $request->input('items');

        foreach ($items as $item) {

            $bookingSnack = BookingSnack::find($item['booking_snack_id']);

            if (!$bookingSnack) {
                continue; 
            }

            $newReceivedQuantity = $bookingSnack->received_quantity + $item['quantity'];

        
            if ($newReceivedQuantity > $bookingSnack->quantity) {
                $newReceivedQuantity = $bookingSnack->quantity;
            }

            $bookingSnack->received_quantity = $newReceivedQuantity;
            $bookingSnack->save();
        }

        if(session()->has('bookingData')){
            $bookingData = session('bookingData');
            $booking_id = $bookingData['booking_id'];

            $snacks = BookingSnack::where('booking_id', $booking_id)
                        ->with('snack')
                        ->get();

            $snacksAmount = $snacks->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $availableSnacks = $snacks->sum(function ($item) {
                return $item->quantity - $item->received_quantity;
            });

            $bookingData['booking_snacks'] = $snacks;
            $bookingData['snacks_amount'] = $snacksAmount;
            $bookingData['available_snacks'] = $availableSnacks;

            session(['bookingData' => $bookingData]);
        }

        return response()->json(['message' => 'Snack collection updated successfully.']);
    }
    
}

