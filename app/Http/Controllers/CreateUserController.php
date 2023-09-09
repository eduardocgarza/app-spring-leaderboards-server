<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CreateUserController extends Controller
{
    public function store(Request $request)
    {
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
                } else if (!is_string($address)) {
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
    }
}
