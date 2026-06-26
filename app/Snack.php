<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Snack extends Model
{
    protected $table = 'snacks';
    protected $primaryKey = 'idsnacks';

    public function variants()
    {
        return $this->hasMany(SnackVariant::class, 'snacks_idsnacks', 'idsnacks');
    }
}