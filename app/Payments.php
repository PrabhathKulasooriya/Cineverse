<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Bookings;
use App\Movie;
use App\MasterUser;
use App\BookedSeat;

class Payments extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    public $timestamps = true;

    protected $fillable = [
        'bookings_booking_id',
        'email',
        'amount',
    ];

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'bookings_booking_id', 'booking_id');
    }

}
