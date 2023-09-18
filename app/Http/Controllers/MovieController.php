<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Requests\MovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        return view('movies.index', compact('movies'));
    }

    public function store(MovieRequest $request)
    {
        try {
            $movie = Movie::create($request->validated());

            $poster = $request->input('poster');

            // Check if 'poster' is provided in the request
            if ($poster) {
                // Save the 'poster' to the database
                $movie->poster = $poster;
                $movie->save();
            }

            return response()->json([
                'message' => "Successfully added movie {$movie->title} with Movie_ID {$movie->id}",
                'success' => true,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Failed to add movie {$movie->title} with Movie_ID {$movie->id}",
                'success' => false,
            ], 500);
        }
    }

    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        // Validation rules will be applied as defined in UpdateMovieRequest
        // Update the movie's attributes
        $movie->update($request->validated());

        $poster = $request->input('poster');

        // Check if 'poster' is provided in the request
        if ($poster) {
            // Save the 'poster' to the database
            $movie->poster = $poster;
            $movie->save();
        }

        return response()->json(['message' => 'Movie data updated successfully']);
    }

    public function destroy(Movie $movie)
    {

        // Delete the associated poster file (if it exists)
        if (!empty($movie->poster)) {
            // Assuming the posters are stored in the 'public/posters' directory
            $posterPath = public_path('posters/' . $movie->poster);

            // Check if the poster file exists and then delete it
            if (file_exists($posterPath)) {
                unlink($posterPath);
            }
        }

        $movie->delete();

        return response()->json(['message' => 'Movie deleted successfully']);
    }

    public function getNewMovies(Request $request)
    {
        $rDate = $request->input('r_date');

        // Query the database to retrieve movies released before the specified date
        $newMovies = Movie::where('release', '<=', $rDate)
            ->orderBy('release', 'desc') // Sort by release date, newest first
            ->get();

        // Calculate the overall rating for each movie
        $newMovies->each(function ($movie) {
            $movie->overall_rating = $movie->calculateOverallRating();
        });

        // Format the response data
        $formattedMovies = $newMovies->map(function ($movie) {
            return [
                'Movie_ID' => $movie->id,
                'Title' => $movie->title,
                'Genre' => $movie->genre_1,
                'Duration' => $movie->length . ' minutes',
                'Views' => $movie->formattedViews(),
                'Poster' => $movie->poster,
                'Overall_rating' => number_format($movie->overall_rating, 1),
                'Description' => $movie->description,
            ];
        });

        return response()->json(['data' => $formattedMovies]);
    }

    public function searchMoviesByPerformer(Request $request)
    {
        $performerName = $request->input('performer_name');

        // Query the database to find movies that have any of the performers matching the 'performer_name'
        $movies = Movie::where('performer_1', 'like', '%' . $performerName . '%')
            ->orWhere('performer_2', 'like', '%' . $performerName . '%')
            ->orWhere('performer_3', 'like', '%' . $performerName . '%')
            ->get();

        // Calculate the overall rating for each movie
        $movies->each(function ($movie) {
            $movie->overall_rating = $movie->calculateOverallRating();
        });

        // Format the response data
        $formattedMovies = $movies->map(function ($movie) {
            return [
                'Movie_ID' => $movie->id,
                'Title' => $movie->title,
                'Genre' => $movie->genre_1,
                'Duration' => $movie->formattedDuration(),
                'Views' => $movie->formattedViews(),
                'Poster' => $movie->poster,
                'Overall_rating' => number_format($movie->overall_rating, 1),
                'Description' => $movie->description,
            ];
        });

        return response()->json(['movies' => $formattedMovies]);
    }

    public function getMoviesByGenre(Request $request)
    {
        // Validate the request data
        $request->validate([
            'genre' => 'required|string',
        ]);

        // Get the genre from the request
        $genre = $request->input('genre');

        // Query the database to retrieve movies of the specified genre
        $movies = Movie::where(function ($query) use ($genre) {
            $query->where('genre_1', $genre)
                ->orWhere('genre_2', $genre)
                ->orWhere('genre_3', $genre);
        })
            ->get();


        // Calculate the overall rating for each movie
        $movies->each(function ($movie) {
            $movie->overall_rating = $movie->calculateOverallRating();
        });


        // Format the response data
        $formattedMovies = $movies->map(function ($movie) {
            return [
                'Movie_ID' => $movie->id,
                'Title' => $movie->title,
                'Genre' => implode(',', array_filter([$movie->genre_1, $movie->genre_2, $movie->genre_3])), // Combine non-empty genre fields
                'Duration' => $movie->formattedDuration(),
                'Views' => $movie->formattedViews(),
                'Poster' => $movie->poster,
                'Overall_rating' => number_format($movie->overall_rating, 1),
                'Description' => $movie->description,
            ];
        });

        return response()->json(['data' => $formattedMovies]);
    }
}
