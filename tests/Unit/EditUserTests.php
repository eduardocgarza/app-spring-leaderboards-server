<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class EditUserTests extends TestCase
{
    /**
     * Generate the route for editing a user.
     *
     * @param mixed $userID The user ID.
     * @return string The generated route.
     */
    private function generateRoute($userID): string
    {
        return generateEndpoint("/users/{$userID}");
    }

    /**
     * Send request with invalid userID.
     * It: Sends a userID in the params that is not a valid integer.
     * Expect: Edit User Fails (404)
     */
    public function testSendRequestWithInvalidUserID(): void
    {
        $route = $this->generateRoute("cvjxchvjxchvhx");
        $response = $this->put($route, []);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Send request for an unknown userID.
     * It: Sends a userID in the params that is a valid integer,
     * but the user does not exist.
     * Expect: Edit User Fails (404)
     */
    public function testSendRequestForUnknownUserID(): void
    {
        $user = User::factory()->create();
        $id = $user->id;
        $user->delete();

        $route = $this->generateRoute($id);
        $response = $this->put($route, [
            'firstName' => 'UpdatedFirstName',
        ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Send request for a valid userID and an empty body.
     * It: Sends a userID in the params that is a valid integer
     * and the user exists and attempts to edit a user with an empty body, intended to fail.
     * Expect: Edit User Fails (404)
     */
    public function testSendRequestForValidUserIDWithEmptyBody(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, []);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->delete();
    }

    /**
     * Send request for a valid userID and an empty first name.
     * It: Sends a userID in the params that is a valid integer
     * and the user exists and attempts to edit a user with a body
     * and firstName empty, intended to fail.
     * Expect: Edit User Fails (404)
     */
    public function testSendRequestForValidUserIDWithEmptyFirstName(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, ['firstName' => '']);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->delete();
    }

    /**
     * Send request for a valid userID and firstName.length > 50.
     * It: Sends a userID in the params that is a valid integer
     * and the user exists and attempts to edit a user with a firstName
     * length greater than 50, intended to fail.
     * Expect: Edit User Fails (404)
     */
    public function testSendRequestForValidUserIDWithLongFirstName(): void
    {
        $user = User::factory()->create();
        $longFirstName = str_repeat('A', 51);

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, ['firstName' => $longFirstName]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->delete();
    }

    /**
     * Send request for a valid userID and successfully edit a user.
     * It: Sends a userID in the params that is a valid integer
     * and the user exists and successfully edits a user.
     * Expect: Edit User Succeeds (200)
     */
    public function testSendRequestForValidUserIDAndSuccessfulEdit(): void
    {
        $user = User::factory()->create();
        $newFirstName = 'UpdatedFirstName';

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, ['firstName' => $newFirstName]);
        $response->assertStatus(Response::HTTP_OK);

        $user->delete();
    }

    /**
     * Test editing a user with an empty dateBirth.
     */
    public function testEditUserWithEmptyDateBirth(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'dateBirth' => '',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->delete();
    }
    /**
     * Test editing a user with an invalid dateBirth (random string).
     */
    public function testEditUserWithInvalidDateBirth(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'dateBirth' => 'random_string',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $user->delete();
    }

    /**
     * Test editing a user with an invalid dateBirth ('2020-05', no day).
     */
    public function testEditUserWithInvalidDateBirthNoDay(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'dateBirth' => '2020-05',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->delete();
    }

    /**
     * Test editing a valid user with dateBirth (Y-m-d).
     */
    public function testEditValidUserWithDateBirth(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'dateBirth' => '1990-01-15',
        ]);
        $response->assertStatus(Response::HTTP_OK);

        $user->delete();
    }

    /**
     * Test editing a user with an empty address.
     */
    public function testEditUserWithEmptyAddress(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'address' => '',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->delete();
    }

    /**
     * Test editing a user with an invalid address (number, not string).
     */
    public function testEditUserWithInvalidAddress(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'address' => 12345,
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->delete();
    }

    /**
     * Test editing a user with an invalid address (length > 255).
     */
    public function testEditUserWithInvalidLongAddress(): void
    {
        $longAddress = str_repeat('a', 256);
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'address' => $longAddress,
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $user->delete();
    }

    /**
     * Test editing a valid user with an address.
     */
    public function testEditValidUserWithAddress(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'address' => '123 Main St',
        ]);
        $response->assertStatus(Response::HTTP_OK);

        $user->delete();
    }

    /**
     * Test editing a user with valid firstName and address.
     */
    public function testEditUserWithValidFirstNameAndAddress(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'firstName' => 'UpdatedFirstName',
            'address' => '123 Main St',
        ]);
        $response->assertStatus(Response::HTTP_OK);

        $user->delete();
    }

    /**
     * Test editing a user with valid dateBirth and address.
     */
    public function testEditUserWithValidDateBirthAndAddress(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'dateBirth' => '1990-01-15',
            'address' => '123 Main St',
        ]);
        $response->assertStatus(Response::HTTP_OK);

        $user->delete();
    }

    /**
     * Test editing a user with valid firstName, dateBirth, and address.
     */
    public function testEditUserWithValidFirstNameDateBirthAndAddress(): void
    {
        $user = User::factory()->create();

        $route = $this->generateRoute($user->id);
        $response = $this->put($route, [
            'firstName' => 'UpdatedFirstName',
            'dateBirth' => '1990-01-15',
            'address' => '123 Main St',
        ]);
        $response->assertStatus(Response::HTTP_OK);

        $user->delete();
    }
}
