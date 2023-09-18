<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieTheater extends Model
{
    use HasFactory;

    protected $table = 'movie_theater';

    protected $fillable = [
        'movie_id',
        'theater_id',
        'd_date',
        'start_time', // Add start_time
        'end_time',   // Add end_time
        'theater_room_no', // Add theater_room_no
    ];
}
