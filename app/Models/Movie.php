<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'release',
        'length',
        'description',
        'mpaa_rating',
        'genre_1',
        'genre_2',
        'genre_3',
        'director',
        'performer_1',
        'performer_2',
        'performer_3',
        'language',
        'poster',
        'overall_rating',
        'views',
    ];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function theaters()
    {
        return $this->belongsToMany(Theater::class, 'movie_theater', 'movie_id', 'theater_id')
            ->withPivot('d_date')
            ->using(MovieTheater::class);
    }

    public function formattedDuration()
    {
        $hours = floor($this->length / 60);
        $minutes = $this->length % 60;

        $formattedDuration = '';

        if ($hours > 0) {
            $formattedDuration .= $hours . ' hour';
            if ($hours > 1) {
                $formattedDuration .= 's';
            }
            $formattedDuration .= ' ';
        }

        if ($minutes > 0) {
            $formattedDuration .= $minutes . ' minute';
            if ($minutes > 1) {
                $formattedDuration .= 's';
            }
        }

        return $formattedDuration;
    }

    public function formattedViews()
    {
        $views = $this->views;

        if ($views >= 1000) {
            // If views are 1,000 or more, format it as X.Xk
            return number_format($views / 1000, 1) . 'k';
        }

        return $views;
    }

    public function calculateOverallRating()
    {
        $ratings = Rating::where('movie_title', $this->title)->pluck('rating');
        $overallRating = $ratings->isEmpty() ? 0 : $ratings->average();
        $formattedRating = number_format($overallRating, 1); // Format to one decimal place
        $this->overall_rating = $formattedRating; // Set the overall_rating attribute
        $this->save(); // Save the model to update the overall_rating in the database
        return $formattedRating;
    }

    // Method to increment the view count for a movie
    public function incrementViews()
    {
        $this->increment('views');
    }

    // Method to retrieve the view count for a movie
    public function getViews()
    {
        return $this->views;
    }
}
