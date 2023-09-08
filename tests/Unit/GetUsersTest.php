<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class GetUsersTest extends TestCase
{
    public function testGetAllUsersReturnsEmptyArray(): void
    {
        User::truncate();
        $route = generateEndpoint("/users");
        $response = $this->get($route);

        $response->assertStatus(Response::HTTP_OK)
            ->assertExactJson([]);
    }

    public function testGetAllUsersWithFactoryData(): void
    {
        User::truncate();
        User::factory(5)->create();

        $route = generateEndpoint("/users");
        $response = $this->get($route);

        // Assert that the response has a 200 OK status code
        // and the JSON response structure matches the expected structure
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "*" => [
                    "userID",
                    "username",
                    "firstName",
                    "points",
                    "dateBirth",
                    "address"
                ],
            ]);

        // Delete all items again after the assertion
        User::truncate();
    }
}
