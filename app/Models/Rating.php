<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_title', // Change to match your schema
        'username',    // Change to match your schema
        'rating',
        'r_description',
    ];

    // Since we are not connecting to any relations directly
    // public function movie()
    // {
    //     return $this->belongsTo(Movie::class);
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
