<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingSnack extends Model
{
    protected $table = 'booking_snacks';
    protected $primaryKey = 'idbooking_snacks';
    
    public function variant()
    {
        return $this->belongsTo(SnackVariant::class, 'idsnack_variants', 'idsnack_variants');
    }
}