<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $seats;
    public $qrcode;
    public $qrType;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $seats, $qrCode, $qrType)
    {
        $this->booking = $booking;
        $this->seats = $seats;
        $this->qrcode = $qrCode;
        $this->qrType = $qrType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Cineverse Ticket')
                    ->view('emails.ticket')
                    ->with([
                    'booking' => $this->booking,
                    'seats' => $this->seats,
                    'qrCode' => $this->qrcode, 
                    'qrType' => $this->qrType,
                ]);
    }
}
