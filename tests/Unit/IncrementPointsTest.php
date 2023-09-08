<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class IncrementPointsTest extends TestCase
{
    /**
     * Send request with invalid userID.
     * It: Sends a userID in the params that is not a valid integer.
     * Expect: Increment Fails (404)
     */
    public function test_send_request_with_invalid_userID(): void
    {
        $route = generateEndpoint("/users/cvjxchvjxchvhx/increment");
        $response = $this->put($route);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Send request for unknown userID.
     * It: Sends a userID in the params that is a valid integer,
     * but the user does not exist.
     * Expect: Increment Fails (404)
     */
    public function test_send_request_for_unknown_userID(): void
    {
        $user = User::factory()->create();
        $id = $user->id;
        $user->delete();

        $route = generateEndpoint("/users/{$id}/increment");
        $response = $this->put($route);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Send request for valid userID and successfully increment points.
     * It: Sends a userID in the params that is valid, the user exists,
     * and increments the user's points.
     * Expect: Increment Succeeds (200)
     */
    public function test_send_request_for_valid_userID_and_successful_increment(): void
    {
        $user = User::factory()->create();

        $route = generateEndpoint("/users/{$user->id}/increment");
        $response = $this->put($route);
        $response->assertStatus(Response::HTTP_OK);

        $user->delete();
    }
}
