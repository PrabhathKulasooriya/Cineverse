<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Movies;

class Shows extends Model
{
    protected $table = 'shows';
    protected $primaryKey = 'show_id';

    public function movies()
     {
        return $this->belongsTo(Movies::class,'movies_movie_id', 'movie_id');
    }
    
}
