<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Seats extends Model
{
    protected $table = 'seats';
    protected $primaryKey = 'seat_id';
    public $timestamps = true;

    protected $fillable = [
        'seat_id',
        'row',
        'number',
        'seat_type_idseat_type',
    ];

    public function bookedSeats()
    {
        return $this->hasMany(BookedSeats::class, 'seats_seat_id', 'seat_id');
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class, 'seat_type_idseat_type', 'idseat_type');
    }
}
