<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theater extends Model
{
    use HasFactory;

    protected $fillable = [
        'theater_name',
    ];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_theater')
            ->withPivot('d_date')
            ->withTimestamps();
    }
}
