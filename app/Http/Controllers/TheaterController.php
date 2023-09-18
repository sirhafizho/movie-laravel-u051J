<?php

namespace App\Http\Controllers;

use App\Models\Theater;
use Illuminate\Http\Request;
use App\Http\Requests\TheaterRequest;
use Carbon\Carbon;

class TheaterController extends Controller
{
    public function index()
    {
        $theaters = Theater::all();
        return response()->json($theaters);
    }

    public function store(TheaterRequest $request)
    {
        $theater = Theater::create($request->validated());
        return response()->json($theater, 201);
    }

    public function show(Theater $theater)
    {
        return response()->json($theater);
    }

    public function update(TheaterRequest $request, Theater $theater)
    {
        $theater->update($request->validated());
        return response()->json($theater);
    }

    public function destroy(Theater $theater)
    {
        $theater->delete();
        return response()->json(['message' => 'Theater deleted successfully']);
    }

    public function associateMoviesWithTheater(Request $request)
    {
        // Validate the request data
        $request->validate([
            'theaterID' => 'required|exists:theaters,id',
            'movieId' => 'required|exists:movies,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s', // Validate start_time
            'end_time' => 'required|date_format:H:i:s',   // Validate end_time
            'theater_room_no' => 'required|integer',      // Validate theater_room_no
        ]);

        // Get the theater by ID
        $theater = Theater::findOrFail($request->input('theaterID'));

        // Store common values
        $commonValues = [
            'd_date' => $request->input('date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'theater_room_no' => $request->input('theater_room_no'),
        ];

        // Attach the movie with the common values
        $theater->movies()->attach($request->input('movieId'), $commonValues);

        // Optionally, you can return a response indicating success
        return response()->json(['message' => 'Movies associated with the theater successfully']);
    }

    public function getSpecificMovieTheater(Request $request)
    {
        // Validate the request data
        $request->validate([
            'theater_name' => 'required|string',
            'd_date' => 'required|date_format:Y-m-d',
        ]);

        // Get the input parameters from the request
        $theaterName = $request->input('theater_name');
        $dDate = Carbon::parse($request->input('d_date'));


        // Find the theater by name
        $theater = Theater::where('theater_name', $theaterName)->first();

        if (!$theater) {
            return response()->json(['message' => 'Theater not found'], 404);
        }

        // Get movies for the specified theater, date, and include the pivot data
        $movies = $theater->movies()
            ->whereHas('theaters', function ($query) use ($dDate) {
                $query->where('d_date', $dDate);
            })
            ->withPivot('start_time', 'end_time', 'theater_room_no')
            ->get();

        // Calculate the overall rating for each movie
        $movies->each(function ($movie) {
            $movie->overall_rating = $movie->calculateOverallRating();
        });

        // Format the response data
        $formattedMovies = $movies->map(function ($movie) use ($theater) {
            return [
                'Movie_ID' => $movie->id,
                'Title' => $movie->title,
                'Duration' => $movie->formattedDuration(),
                'Views' => $movie->formattedViews(),
                'Genre' => $movie->genre_1,
                'Poster' => $movie->poster,
                'Overall_rating' => number_format($movie->overall_rating, 1),
                'Theater_name' => $theater->theater_name,
                'Start_time' => $movie->pivot->start_time,
                'End_time' => $movie->pivot->end_time,
                'Description' => $movie->description,
                'Theater_room_no' => $movie->pivot->theater_room_no,
            ];
        });

        return response()->json(['data' => $formattedMovies]);
    }

    public function TimeSlot(Request $request)
    {
        // Validate the request data
        $request->validate([
            'theater_name' => 'required|string',
            'time_start' => 'required|date_format:Y-m-d H:i:s',
            'time_end' => 'required|date_format:Y-m-d H:i:s|after:time_start',
        ]);

        // Get the input parameters from the request
        $theaterName = $request->input('theater_name');
        $timeStart = Carbon::parse($request->input('time_start'));
        $timeEnd = Carbon::parse($request->input('time_end'));

        // Find the theater by name
        $theater = Theater::where('theater_name', $theaterName)->first();

        if (!$theater) {
            return response()->json(['message' => 'Theater not found'], 404);
        }

        // Get movies for the specified theater and time window
        $movies = $theater->movies()
            ->where('start_time', '>=', $timeStart)
            ->where('end_time', '<=', $timeEnd)
            ->get();

        // Calculate the overall rating for each movie
        $movies->each(function ($movie) {
            $movie->overall_rating = $movie->calculateOverallRating();
        });

        // Format the movies data
        $formattedMovies = $movies->map(function ($movie) {
            return [
                'Movie_ID' => $movie->id,
                'Title' => $movie->title,
                'Duration' => $movie->formattedDuration(),
                'Views' => $movie->formattedViews(),
                'Genre' => $movie->genre_1,
                'Poster' => $movie->poster,
                'Overall_rating' => number_format($movie->overall_rating, 1),
                'Theater_name' => $movie->pivot->theater_name,
                'Start_time' => $movie->pivot->start_time,
                'End_time' => $movie->pivot->end_time,
                'Description' => $movie->description,
                'Theater_room_no' => $movie->pivot->theater_room_no,
            ];
        });

        return response()->json(['data' => $formattedMovies]);
    }
}
