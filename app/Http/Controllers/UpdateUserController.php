<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UPDATEUSERCONTROLLER extends Controller
{
    public function update($userID, Request $request)
    {
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
                    } else if (!is_string($value)) {
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
    }
}
