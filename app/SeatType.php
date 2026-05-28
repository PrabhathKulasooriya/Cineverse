<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SeatType extends Model
{
    protected $table = 'seat_type';
    protected $primaryKey = 'idseat_type';
    public $timestamps = true;

    protected $fillable = [
        'idseat_type',
        'type',
        'price',
    ];

}
