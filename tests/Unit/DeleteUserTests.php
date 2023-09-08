<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class DeleteUserTests extends TestCase
{
    /**
     * Send request with invalid userID.
     * It: Sends a userID in the params that is not a valid integer.
     * Expect: Delete User Fails (404)
     */
    public function test_send_request_with_invalid_userID(): void
    {
        $route = generateEndpoint("/users/cvjxchvjxchvhx");
        $response = $this->delete($route);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Send request for unknown userID.
     * It: Sends a userID in the params that is a valid integer,
     * but the user does not exist.
     * Expect: Delete User Fails (404)
     */
    public function test_send_request_for_unknown_userID(): void
    {
        $user = User::factory()->create();
        $id = $user->id;
        $user->delete();

        $route = generateEndpoint("/users/{$id}");
        $response = $this->delete($route);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Send request for valid userID and successfully delete a user.
     * It: Sends a userID in the params that is a valid integer
     * and the user exists and successfully deletes the user.
     * Expect: Delete User Succeeds (204)
     */
    public function test_send_request_for_valid_userID_and_successful_delete(): void
    {
        $user = User::factory()->create();

        $route = generateEndpoint("/users/{$user->id}");
        $response = $this->delete($route);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
