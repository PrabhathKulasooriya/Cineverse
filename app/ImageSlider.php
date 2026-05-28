<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageSlider extends Model
{
    protected $table = 'imageslider';
    protected $primaryKey = 'idimageSlider';

    public function movie()
        {
            return $this->belongsTo(Movies::class, 'movies_movie_id', 'movie_id');
        }
}
