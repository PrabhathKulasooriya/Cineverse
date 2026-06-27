<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingSnack extends Model
{
    protected $table = 'booking_snacks';
    protected $primaryKey = 'idbooking_snacks';
    
    public function snack()
    {
        return $this->belongsTo(Snack::class, 'snacks_idsnacks', 'idsnacks');
    }
}