<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Show;
use App\Movie;
use App\MasterUser;
use App\BookedSeat;

class Bookings extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';
    public $timestamps = true;

    protected $fillable = [
        'shows_show_id',
        'movies_movie_id',
        'master_user_idmaster_user',
        'customer_name',
        'amount',
        'payment_status',
    ];

    public function show()
    {
        return $this->belongsTo(Shows::class, 'shows_show_id', 'show_id');
    }

    public function movie()
    {
        return $this->belongsTo(Movies::class, 'movies_movie_id', 'movie_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'master_user_idmaster_user', 'idmaster_user');
    }

    public function bookedSeats()
    {
        return $this->hasMany(BookedSeats::class, 'bookings_booking_id', 'booking_id');
    }
}
