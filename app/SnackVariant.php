<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SnackVariant extends Model
{
    protected $table = 'snack_variants';
    protected $primaryKey = 'idsnack_variants';

    public function snack()
    {
        return $this->belongsTo(Snack::class, 'snacks_idsnacks', 'idsnacks');
    }
    
}