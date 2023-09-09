<?php

use App\Http\Controllers\CreateUserController;
use App\Http\Controllers\DecrementUserPointsController;
use App\Http\Controllers\DeleteUserController;
use App\Http\Controllers\IncrementUserPointsController;
use App\Http\Controllers\UpdateUserController;
use App\Http\Controllers\GetUsersController;
use Illuminate\Support\Facades\Route;

require_once app_path('Helpers/helpers.php');

// Get All User -- GET /users
Route::get("/users", [GetUsersController::class, 'index']);

// Create User -- POST /users
Route::post("/users", [CreateUserController::class, 'store']);

// Edit User -- PUT /users/{userID}
Route::put("/users/{userID}", [UpdateUserController::class, 'update']);

// Delete User -- DELETE /users/{userID}
Route::delete("/users/{userID}", [DeleteUserController::class, 'destroy']);

// Increment User Points by 1 -- PUT /users/{userID}
Route::put("/users/{userID}/increment", [IncrementUserPointsController::class, 'increment']);

// Decrement User Points by 1 -- PUT /users/{userID}
Route::put("/users/{userID}/decrement", [DecrementUserPointsController::class, 'decrement']);

