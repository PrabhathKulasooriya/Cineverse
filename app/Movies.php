<?php

namespace App; 

use Illuminate\Database\Eloquent\Model;
use App\ImageSlider;
use App\Shows;

class Movies extends Model
{
    protected $table = 'movies';
    protected $primaryKey = 'movie_id';

    protected $fillable = [
        'movie_id',
        'name',
        'category',
        'duration',
        'language',
        'image',
        'rating',
        'trailer',
        'relese_date',
        'status',
        'created_at',
        'updated_at',
        'screening_status'
    ];

    public function sliders()
     {
        return $this->hasMany(ImageSlider::class, 'movies_movie_id', 'movie_id');
    }
    public function shows(){
        return $this->hasMany(Shows::class, 'movies_movie_id', 'movie_id');
    }
    
}