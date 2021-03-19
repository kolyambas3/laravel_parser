<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    //
    protected $fillable = [
        'title', 'release_date', 'rating', 'cover_image', 'director'
    ];
}
