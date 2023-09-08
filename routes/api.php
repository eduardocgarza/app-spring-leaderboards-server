<?php

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

require_once app_path('Helpers/helpers.php');

/**
 * GET /users
 */
Route::get("/users", function (Request $request) {
    $users = User::orderBy('points', 'desc')->get();
    $formattedUsers = UserResource::collection($users)->toArray($request);
    return $formattedUsers;
});

/**
 * -- Create User Item
 *
 * POST /users
 *
 */
Route::post("/users", function (Request $request) {
    try {
        $request->validate([
            'firstName' => 'required|string|max:50',
            'dateBirth' => 'nullable|date_format:Y-m-d',
            'address' => 'nullable|string|max:255',
        ]);

        // Get the user's first name from the request
        $firstName = $request->input('firstName');

        // Generate Username
        $username = generateUsername($firstName);

        // Create a new user record
        $user = new User();
        $user->username = $username;
        $user->first_name = $request->input('firstName');
        $user->points = 0;

        // Check if dateBirth is provided and not an empty string
        if ($request->has('dateBirth')) {
            $dateBirth = $request->input('dateBirth');

            if ($dateBirth === '' || $dateBirth === null) {
                $error = ['error' => 'The dateBirth field cannot be empty.'];
                return response()->json($error, Response::HTTP_BAD_REQUEST);
            }
            $user->date_birth = $dateBirth;
        }

        // Check if address is provided and not an empty string
        if ($request->has('address')) {
            $address = $request->input('address');

            if ($address === '' || $address === null) {
                $error = ['error' => 'The address field cannot be empty.'];
                return response()->json($error, Response::HTTP_BAD_REQUEST);
            }
            else if (!is_string($address)) {
                $error = ['error' => 'The address field must be a string.'];
                return response()->json($error, Response::HTTP_BAD_REQUEST);
            }
            $user->address = $address;
        }

        // Save the user record to the database
        $user->save();

        // Format the new user record as a UserObject
        $userObject = [
            'userID' => $user->id,
            'username' => $user->username,
            'firstName' => $user->first_name,
            'points' => (int) $user->points,
            'dateBirth' => $user->date_birth,
            'address' => $user->address,
        ];

        // Return the UserObject as the response
        return response()->json($userObject, Response::HTTP_CREATED);
    } catch (ValidationException $e) {
        // Handle validation errors
        return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    }
});

/**
 * -- Edit User Item
 *
 * PUT /users/{userID}
 *
 */
Route::put("/users/{userID}", function ($userID, Request $request) {
    if (!is_numeric($userID) || !ctype_digit($userID)) {
        $error = ["error" => "The userID field must be a valid integer."];
        return response()->json($error, Response::HTTP_BAD_REQUEST);
    }

    // Check if there is at least one valid property in the request body
    $validProperties = ['firstName', 'dateBirth', 'address'];
    $hasValidProperty = false;

    foreach ($validProperties as $property) {
        $value = $request->input($property);
        if ($value !== null) {
            $hasValidProperty = true;

            // Validate firstName
            if ($property === 'firstName') {
                if (!is_string($value) || strlen($value) > 50) {
                    $error = ["error" => "The firstName field must be a string of at most 50 characters."];
                    return response()->json($error, Response::HTTP_BAD_REQUEST);
                }
            }

            // Validate dateBirth
            if ($property === 'dateBirth' && $value !== null) {
                if (!DateTime::createFromFormat('Y-m-d', $value)) {
                    $error = ["error" => "Invalid date format for dateBirth. Use Y-m-d format (e.g., 2023-10-15)."];
                    return response()->json($error, Response::HTTP_BAD_REQUEST);
                }
            }

            // Validate address
            if ($property === 'address' && $value !== null) {
                if (strlen($value) > 255) {
                    $error = ["error" => "The address field must be at most 255 characters."];
                    return response()->json($error, Response::HTTP_BAD_REQUEST);
                }
                else if(!is_string($value)) {
                    $error = ["error" => "The address field must be a string."];
                    return response()->json($error, Response::HTTP_BAD_REQUEST);
                }
            }
        }
    }

    if (!$hasValidProperty) {
        $error = ["error" => "At least one valid property (firstName, dateBirth, or address) must be provided in the request body."];
        return response()->json($error, Response::HTTP_BAD_REQUEST);
    }

    // Check if the user exists
    $user = User::find($userID);
    if (!$user) {
        $error = ["error" => "There is no user found with this userID."];
        return response()->json($error, Response::HTTP_NOT_FOUND);
    }

    // Get and validate firstName
    $firstName = $request->input('firstName');
    if ($firstName !== null) {
        if (!is_string($firstName) || strlen($firstName) > 50) {
            $error = ["error" => "The firstName field must be a string of at most 50 characters."];
            return response()->json($error, Response::HTTP_BAD_REQUEST);
        }
        $user->first_name = $firstName;
        $user->username = generateUsername($firstName);
    }

    // Get and validate dateBirth
    $dateBirth = $request->input('dateBirth');
    if ($dateBirth !== null) {
        if (!strtotime($dateBirth)) {
            $error = ["error" => "Invalid date format for dateBirth."];
            return response()->json($error, Response::HTTP_BAD_REQUEST);
        }
        $user->date_birth = $dateBirth;
    }

    // Get and validate address
    $address = $request->input('address');
    if ($address !== null) {
        if (strlen($address) > 255) {
            $error = ["error" => "The address field must be at most 255 characters."];
            return response()->json($error, Response::HTTP_BAD_REQUEST);
        }
        $user->address = $address;
    }

    $user->save();

    // Format the new user record as a UserObject
    $userObject = [
        'userID' => $user->id,
        'username' => $user->username,
        'firstName' => $user->first_name,
        'dateBirth' => $user->date_birth,
        'address' => $user->address,
        'points' => (int) $user->points,
    ];

    // Return the UserObject as the response
    return response()->json($userObject, Response::HTTP_OK);
});

/**
 * -- Delete User Item
 *
 * DELETE /users/{userID}
 *
 */
Route::delete("/users/{userID}", function ($userID) {
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

    // Delete the user
    $user->delete();

    // Return a 204 Deleted response
    return new Response(null, Response::HTTP_NO_CONTENT);
});

/**
 * -- Increment Points
 *
 * PUT /users/{userID}/increment
 *
 */
Route::put("/users/{userID}/increment", function ($userID) {
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
});

/**
 * -- Decrement Points
 *
 * PUT /users/{userID}/decrement
 *
 */
Route::put("/users/{userID}/decrement", function ($userID) {
    if (!is_numeric($userID) || !ctype_digit($userID)) {
        $error = ["error" => "The userID field must be a valid integer."];
        return response()->json($error, Response::HTTP_BAD_REQUEST);
    }

    // Check if the user exists
    $user = User::find($userID);
    if (!$user) {
        $error = ["error" => "There is no user found with this userID."];
        return response()->json($error, Response::HTTP_NOT_FOUND);
    }

    // Decrement points by 1, but ensure it doesn't go below 0
    if ($user->points > 0) {
        $user->points -= 1;
        $user->save();
    }

    // Return a 200 OK response
    return new Response(null, Response::HTTP_OK);
});
