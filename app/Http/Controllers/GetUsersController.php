<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;

class GetUsersController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('points')->orderBy('username')->get();
        $formattedUsers = UserResource::collection($users);

        return $formattedUsers;
    }
}
