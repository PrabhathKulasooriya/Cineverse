<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Snack extends Model
{
    protected $table = 'snacks';
    protected $primaryKey = 'idsnacks';

    public function bookingSnacks()
    {
        return $this->hasMany(BookingSnack::class, 'snacks_idsnacks', 'idsnacks');
    }
}