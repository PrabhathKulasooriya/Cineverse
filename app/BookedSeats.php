<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Booking;
use App\Seat;

class BookedSeats extends Model
{
    protected $table = 'booked_seats';
    protected $primaryKey = 'booked_seats_id';
    public $timestamps = true;

    protected $fillable = [
        'bookings_booking_id',
        'seats_seat_id',
    ];

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'bookings_booking_id', 'booking_id');
    }

    public function seat()
    {
        return $this->belongsTo(Seats::class, 'seats_seat_id', 'seat_id');
    }
}
