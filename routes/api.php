<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TheaterController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Movie CRUD
Route::post('/movies', [MovieController::class, 'store']);
Route::put('/movies/{movie}', [MovieController::class, 'update']);
Route::delete('/movies/{movie}', [MovieController::class, 'destroy']);

// Movie Query
// New Movies Get
Route::get('/new-movies', [MovieController::class, 'getNewMovies']);
// Query by Performer 
Route::get('/movies/search', [MovieController::class, 'searchMoviesByPerformer']);
// Query by Genre
Route::get('/movies/genre', [MovieController::class, 'getMoviesByGenre']);

// Theater
// CRUD
// index: Lists all theaters.
// store: Creates a new theater.
// show: Gets details of a specific theater.
// update: Updates a specific theater.
// destroy: Deletes a specific theater.
Route::resource('theaters', TheaterController::class);
// Associate Movies with Theaters
Route::post('/theaters/associate-movies', [TheaterController::class, 'associateMoviesWithTheater']);
// Query by movie theater
Route::get('/specific-movie-theater', [TheaterController::class, 'getSpecificMovieTheater']);
// Query by time-slot
Route::get('/time-slot', [TheaterController::class, 'TimeSlot']);

// Ratings
Route::post('/ratings', [RatingController::class, 'giveRating']);
