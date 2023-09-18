<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRatingRequest;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function giveRating(CreateRatingRequest $request)
    {
        // Validate the request data using the CreateRatingRequest validation rules.
        $validatedData = $request->validated();

        // Check if the 'rating' field is a valid numerical value between 1 and 10.
        if (!is_numeric($validatedData['rating']) || $validatedData['rating'] < 1 || $validatedData['rating'] > 10) {
            return response()->json([
                'message' => 'Invalid rating. Rating must be a numerical value between 1 and 10.',
                'success' => false,
            ], 400);
        }

        // Create a new rating record in the database.
        $rating = Rating::create([
            'movie_title' => $validatedData['movie_title'],
            'username' => $validatedData['username'],
            'rating' => $validatedData['rating'],
            'r_description' => $validatedData['r_description'],
        ]);

        // Check if the rating was successfully created.
        if ($rating) {
            // Assuming you have successfully added the rating
            $movieTitle = $request->input('movie_title');
            $username = $request->input('username');

            $message = "Successfully added review for $movieTitle by user: $username";

            return response()->json([
                'message' => $message,
                'success' => true,
            ], 201);
        } else {
            $movieTitle = $request->input('movie_title');
            $username = $request->input('username');

            $message = "Failed to add review for $movieTitle by user: $username";

            return response()->json([
                'message' => $message,
                'success' => false,
            ], 500);
        }
    }
}
