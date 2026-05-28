<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Movies;
use App\Shows;
use App\BookedSeats;
use App\Bookings;
use App\Payments;
use Stripe\Stripe;
use Exception;






class PaymentController extends Controller
{

    public function stripePayment(Request $request){

        $selectedSeatsId = $request->selectedSeatsId;
        if (is_string($selectedSeatsId)) {
            $selectedSeatsId = json_decode($selectedSeatsId, true);
        }

        // Get customer data
        if (auth()->check()) {
            $customerId = auth()->user()->idmaster_user;
            $customerEmail = auth()->user()->email;
            $customerName = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        } else {
            $customerId = null;
            $customerEmail = null;
            $customerName = null;
        }

        try {
            // Validate that we have valid seats
            if (!is_array($selectedSeatsId) || empty($selectedSeatsId)) {
                throw new \Exception('Invalid seat selection data');
            }

            // Generate a unique booking ID
            $showId = (int) $request->showId;
            $firstSeatId = is_array($selectedSeatsId) 
                ? (int) $selectedSeatsId[0] 
                : (int) $selectedSeatsId;

            $timeComponent = (int) substr(microtime(true) * 10000, -4);

            $bookingId = (int) (
                str_pad($showId, 2, '0', STR_PAD_LEFT) .
                str_pad($firstSeatId, 3, '0', STR_PAD_LEFT) .
                $timeComponent
            );
            
            // Save booking data to database with PENDING status
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
                
                // Skip invalid seat IDs
                if ($seatIdInt <= 0) {
                    \Log::warning('Skipping invalid seat ID: ' . $seatId);
                    continue;
                }
                
                $saveSeats = new BookedSeats();
                $saveSeats->bookings_booking_id = $bookingId;
                $saveSeats->seats_seat_id = $seatIdInt;
                $saveSeats->save();
            }

            $bookingData = [
                'amount' => $request->totalAmount,
                'selectedSeatsId' => $selectedSeatsId,
                'movieId' => $request->movieId,
                'showId' => $request->showId,
                'booking_id' => $bookingId,
            ];
            
            session(['booking_data' => $bookingData]);

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $success_url = route('success').'?session_id={CHECKOUT_SESSION_ID}';

            $selectedSeatsString = is_array($selectedSeatsId) 
            ? implode(',', $selectedSeatsId) 
            : $selectedSeatsId;

        
            $session = $stripe->checkout->sessions->create([
                'success_url' => $success_url,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'lkr',
                            'product_data' => ['name' => 'Movie Ticket'],
                            'unit_amount' => $request->totalAmount * 100, 
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'customer_creation' => 'always',
                'metadata' => [
                'selectedSeatsId' => $selectedSeatsString,
                'movieId' => (string) $request->movieId,
                'showId' => (string) $request->showId,
                'amount' => (string) $request->totalAmount,
                'booking_id' => (string) $bookingId,
                ],
            ]);
            
            return redirect($session->url);
            
        } catch (\Exception $e) {
            \Log::error('Booking creation error: ' . $e->getMessage());
            return redirect()->route('ticketpage')->with('error', 'Booking creation failed. Please try again.');
        }
    }



    public function success(Request $request){
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->retrieve($request->session_id);
        
        // Get booking data from session
        $bookingData = session('booking_data');
        
        if (!$bookingData) {
            return redirect()->route('ticketpage')->with('error', 'Booking data not found!');
        }
        
        // Check if payment was successful
        if ($session->payment_status == 'paid') {

            if (auth()->check()) {
                $customerId = auth()->user()->idmaster_user;
                $customerEmail = auth()->user()->email;
                $customerName = auth()->user()->first_name . ' ' . auth()->user()->last_name;
            } else {
                $customerId = null;
                $customerEmail = $session->customer_details->email ?? null;
                $customerName = $session->customer_details->name ?? null;
            }

            
            try {
                
                $booking = Bookings::where('booking_id', $bookingData['booking_id'])->first();
                if ($booking) {
                    $booking->master_user_idmaster_user = $customerId;
                    $booking->payment_status = 'PAID';
                    $booking->customer_name = strtoupper($customerName);
                    $booking->email = $customerEmail;
                    $booking->save();

                    $payment = new Payments();
                    $payment->bookings_booking_id = $booking->booking_id;
                    $payment->email = $customerEmail;
                    $payment->amount = $booking->amount;
                    $payment->save();
                } else {
                    throw new \Exception('Booking not found for update');
                }
                
                // Store the booking data for the ticket page
                $bookingData['payment_status'] = 'PAID';
                $bookingData['stripe_session_id'] = $session->id;
                $bookingData['customer_email'] = $customerEmail;
                $bookingData['customer_name'] = $customerName;
                
                
                session(['completed_booking' => $bookingData]);
                session()->forget('booking_data');
                
                return redirect()->route('ticketpage')->with('success', 'Payment successful!');
                
            } catch (\Exception $e) {
                \Log::error('Booking update error: ' . $e->getMessage());
                return redirect()->route('ticketpage')->with('error', 'Payment successful but booking update failed. Please contact support.');
            }
            
        } else {
            // Payment failed - clean up the pending booking
            $this->cleanupFailedBooking($bookingData['booking_id']);
            session()->forget('booking_data');
            return redirect()->route('ticketpage')->with('error', 'Payment failed. Please try again.');
        }
    }

    public function cancel(Request $request){
        // Get booking data from session
        $bookingData = session('manual_booking_data');
        if (!$bookingData) {
            return redirect()->back()->with('error', 'Booking data not found!');
        }
         
        $movieId = $bookingData['movieId'];
        $bookingId = $bookingData['booking_id'];

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
            session()->forget('manual_booking_data');
            return redirect("/bookmovie/{$movieId}")
                        ->with('error', 'Payment was cancelled.');}
    }


    private function cleanupFailedBooking($bookingId){
        try {
            // Delete booked seats first (foreign key constraint)
            BookedSeats::where('bookings_booking_id', $bookingId)->delete();
            
            // Delete the booking
            Bookings::where('booking_id', $bookingId)->delete();
            
            \Log::info('Cleaned up failed booking: ' . $bookingId);
        } catch (\Exception $e) {
            \Log::error('Error cleaning up booking ' . $bookingId . ': ' . $e->getMessage());
        }
    }


    public function cleanupExpiredBookings(){
        try {
            
            $expiredBookings = Bookings::where('payment_status', 'PENDING')
                ->where('created_at', '<', now()->subMinutes(20))
                ->get();

            foreach ($expiredBookings as $booking) {
               
                BookedSeats::where('bookings_booking_id', $booking->booking_id)->delete();
                $booking->delete();
                \Log::info('Cleaned up expired booking: ' . $booking->booking_id);
            }

            return response()->json(['message' => 'Expired bookings cleaned up successfully']);
        } catch (\Exception $e) {
            \Log::error('Error cleaning up expired bookings: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to cleanup expired bookings'], 500);
        }
    }
    

//Manual Payment *************************************************************************************************************************************

    public function paymentPage(){

        $bookingData = session('manual_booking_data');
        return view('bookings.paymentPage', compact('bookingData'));
    }



    public function manualPayment(Request $request){

        $paymentMethod = $request->input('paymentMethod');
        $validator = null; // <-- Initialize the variable
    
        if ($paymentMethod == 'CARD') {
    
            $validator = Validator::make($request->all(), [
                'cardNumber' => [
                    'required',
                    'string',
                    'regex:/^\d{4}\s\d{4}\s\d{4}\s\d{4}$/',
                ],
                'expireDate' => [
                    'required',
                    'string',
                    'regex:/^(0[1-9]|1[0-2])\/\d{2}$/',
                ],
                'cvv' => [
                    'required',
                    'string',
                    'digits_between:3,4',
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                ],
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ]
            ], [
                'cardNumber.required' => 'Card number is required.',
                'cardNumber.regex' => 'Card number must be in format: 4242 4242 4242 4242.',
                'expireDate.required' => 'Expiry date is required.',
                'expireDate.regex' => 'Expiry date must be in format: MM/YY.',
                'cvv.required' => 'CVV is required.',
                'cvv.digits_between' => 'CVV must be 3 or 4 digits.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'name.required' => 'Name is required.',
            ]);
    
            
            [$month, $year] = explode('/', $request->expireDate);
            $cardMonth = (int) $month;
            $cardYear = (int) ('20' . $year);

            $cardNumber = $request->input('cardNumber');
            $last4 = substr(str_replace(' ', '', $cardNumber), -4);
            $currentMonth = (int) date('m');
            $currentYear = (int) date('Y');
    
            if ($cardYear < $currentYear || ($cardYear == $currentYear && $cardMonth < $currentMonth)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'The card has expired.');
            }
    
        } elseif ($paymentMethod == 'PAYPAL' || $paymentMethod == 'CASH') {
    
            $validator = Validator::make($request->all(), [
                'email' => [
                    'required',
                    'email',
                    'max:255',
                ],
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ]
            ], [
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'name.required' => 'Name is required.',
            ]);
    
        }
    
        if (!$validator) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unsupported payment method.');
        }
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }
    
        
        try {
            
            $bookingData=session('manual_booking_data');

            $bookingData['payment_status'] = 'PAID';
            $bookingData['customer_email']=$request->email;
            $bookingData['customer_name']=strtoupper($request->name);
            $bookingData['payment_method']=strtoupper($paymentMethod);

            if (auth()->check()) {
                $user_id = auth()->user()->idmaster_user;
            }else{
                $user_id = null;
            }
            
            $booking = Bookings::find($bookingData['booking_id']);

            if(!$booking)
                {return redirect()->route('ticketpage')->with('error', 'Booking not found!');}
            
            if($booking->payment_status == 'PAID')
                {return redirect()->route('ticketpage')->with('error', 'Booking already paid!');}


            $booking->payment_status = 'PAID';
            $booking->customer_name = strtoupper($request->name);
            $booking->email = $request->email;
            $booking->master_user_idmaster_user=$user_id;
            $booking->save();

            $payment = new Payments();
            $payment->bookings_booking_id = $bookingData['booking_id'];
            $payment->email=$request->email;
            $payment->amount=$bookingData['amount'];
            $payment->method=strtoupper($paymentMethod);
            $payment->save();
            

            session(['completed_booking' => $bookingData]);
            session()->forget('manual_booking_data');
            return redirect()->route('ticketpage');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }


//Pending Payments*****************************************************************************************************************

    public function pendingPayments()
    {
        
        $pendingBookings = Bookings::where('payment_status', 'PENDING')->get();

        
        $pendingPaymentsData = [];

        foreach ($pendingBookings as $booking) {
           
            $movie = Movies::find($booking->movies_movie_id);
            $show = Shows::find($booking->shows_show_id);
            $user = User::find($booking->master_user_idmaster_user);

            
            $userName = "GUEST";
            if ($user) { 
                $userName = $user->first_name . " " . $user->last_name;
            }

            
            $pendingPaymentsData[] = [
                'booking_id' => $booking->booking_id,
                'master_user_idmaster_user' => $booking->master_user_idmaster_user,
                'movie' => $movie->name,
                'date' => $show->date,
                'time' => $show->time,
                'userName' => $userName,
                'amount'=>  $booking->amount,
                'payment_status' => $booking->payment_status
            ];
        }

        
        return view('management.pendingPayment', [
            'title' => 'Pending Payments',
            'pendingPayments' => $pendingPaymentsData,
        ]);
    }

    public function cancelPayment(Request $request)
    {   

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