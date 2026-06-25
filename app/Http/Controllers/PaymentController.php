<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Movies;
use App\Shows;
use App\BookedSeats;
use App\Bookings;
use App\Payments;
use Exception;

class PaymentController extends Controller
{
    public function cancel(Request $request)
    {
        // Get booking data from session
        $bookingData = session('manual_booking_data');
        if (!$bookingData) {
            return redirect()->back()->with('error', 'Booking data not found!');
        }
         
        $movieId = $bookingData['movieId'];
        $bookingId = $bookingData['booking_id'];

        if ($bookingId == null) {
            return redirect()->back()->with('error', 'Booking ID not found.');
        }

        $booking = Bookings::find($bookingId);
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        if ($booking->payment_status == 'PAID') {
            return redirect()->back()->with('error', 'Booking already paid.');
        } else if ($booking->payment_status == 'PENDING') {
            BookedSeats::where('bookings_booking_id', $bookingId)->delete();
            $booking->delete();
            session()->forget('manual_booking_data');
            
            return redirect("/bookmovie/{$movieId}")
                        ->with('error', 'Payment was cancelled.');
        }
    }

    public function cleanupExpiredBookings()
    {
        try {
            $expiredBookings = Bookings::where('payment_status', 'PENDING')
                ->where('created_at', '<', now()->subMinutes(20))
                ->get();

            foreach ($expiredBookings as $booking) {
                BookedSeats::where('bookings_booking_id', $booking->booking_id)->delete();
                $booking->delete();
                Log::info('Cleaned up expired booking: ' . $booking->booking_id);
            }

            return response()->json(['message' => 'Expired bookings cleaned up successfully']);
        } catch (Exception $e) {
            Log::error('Error cleaning up expired bookings: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to cleanup expired bookings'], 500);
        }
    }

    // Manual Payment Methods
    // *****************************************************************************************

    public function paymentPage()
    {
        $bookingData = session('manual_booking_data');
        return view('bookings.paymentPage', compact('bookingData'));
    }

    public function manualPayment(Request $request)
    {
        $paymentMethod = $request->input('paymentMethod');
        $validator = null; 
    
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

            $currentMonth = (int) date('m');
            $currentYear = (int) date('Y');
    
            if ($cardYear < $currentYear || ($cardYear == $currentYear && $cardMonth < $currentMonth)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'The card has expired.');
            }
    
        } elseif ($paymentMethod == 'CASH') {
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
            $bookingData = session('manual_booking_data');

            $bookingData['payment_status'] = 'PAID';
            $bookingData['customer_email'] = $request->email;
            $bookingData['customer_name'] = strtoupper($request->name);
            $bookingData['payment_method'] = strtoupper($paymentMethod);

            $user_id = auth()->check() ? auth()->user()->idmaster_user : null;
            
            $booking = Bookings::find($bookingData['booking_id']);

            if(!$booking) {
                return redirect()->route('ticketpage')->with('error', 'Booking not found!');
            }
            
            if($booking->payment_status == 'PAID') {
                return redirect()->route('ticketpage')->with('error', 'Booking already paid!');
            }

            $booking->payment_status = 'PAID';
            $booking->customer_name = strtoupper($request->name);
            $booking->email = $request->email;
            $booking->master_user_idmaster_user = $user_id;
            $booking->save();

            $payment = new Payments();
            $payment->bookings_booking_id = $bookingData['booking_id'];
            $payment->email = $request->email;
            $payment->amount = $bookingData['amount'];
            $payment->method = strtoupper($paymentMethod);
            $payment->save();
            
            session(['completed_booking' => $bookingData]);
            session()->forget('manual_booking_data');
            
            return redirect()->route('ticketpage');
            
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    // Pending Payments Methods
    // *****************************************************************************************

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
                'amount' =>  $booking->amount,
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
            
            return redirect()->back()->with('success', 'Booking canceled successfully.');
        }
    }
}