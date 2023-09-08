<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class DecrementPointsTests extends TestCase
{
    /**
     * Send request with invalid userID.
     * It: Sends a userID in the params that is not a valid integer.
     * Expect: Decrement Fails (404)
     */
    public function test_send_request_with_invalid_userID(): void
    {
        $route = generateEndpoint("/users/cvjxchvjxchvhx/decrement");
        $response = $this->put($route);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Send request for unknown userID.
     * It: Sends a userID in the params that is a valid integer,
     * but the user does not exist.
     * Expect: Decrement Fails (404)
     */
    public function test_send_request_for_unknown_userID(): void
    {
        $user = User::factory()->create();
        $id = $user->id;
        $user->delete();

        $route = generateEndpoint("/users/{$id}/decrement");
        $response = $this->put($route);
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $user->delete();
    }

    /**
     * Send request for valid userID and successfully decrement points.
     * It: Sends a userID in the params that is valid, the user exists,
     * and decrements the user's points from positive to 0.
     * Expect: Decrement Succeeds (200)
     */
    public function test_send_request_for_valid_userID_and_successful_decrement(): void
    {
        $user = User::factory()->create();
        $user->points = 1;
        $user->save();

        $decrementRoute = generateEndpoint("/users/{$user->id}/decrement");
        $response = $this->put($decrementRoute);
        $response->assertStatus(Response::HTTP_OK);

        // Verify that points have become 0
        $this->assertEquals(0, $user->fresh()->points);

        $user->delete();
    }

    /**
     * Send request for valid userID to decrement points starting at 0.
     * It: Sends a valid userID in the params and attempts to decrement points below 0.
     * Expect: Decrement Succeeds - It should fail silently (since points cannot be negative) and return a 200 OK.
     */
    public function test_send_request_for_valid_userID_and_decrement_from_zero_points(): void
    {
        $user = User::factory()->create();
        $user->points = 0;
        $user->save();

        $route = generateEndpoint("/users/{$user->id}/decrement");
        $response = $this->put($route);
        $response->assertStatus(Response::HTTP_OK);

        // Verify that points remain at 0
        $this->assertEquals(0, $user->fresh()->points);

        $user->delete();
    }
}
