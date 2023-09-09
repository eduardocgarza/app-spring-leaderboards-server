<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class IncrementUserPointsController extends Controller
{
    public function increment($userID)
    {
        if (!is_numeric($userID) || !ctype_digit($userID)) {
            $error = ["error" => "The userID field must be a valid integer."];
            return response()->json($error, 400);
        }

        // Check if the user exists
        $user = User::find($userID);
        if (!$user) {
            $error = ["error" => "There is no user found with this userID."];
            return response()->json($error, Response::HTTP_NOT_FOUND);
        }

        // Increment points by 1
        $user->points += 1;
        $user->save();

        // Return a 200 OK response
        return new Response(null, Response::HTTP_OK);
    }
}
