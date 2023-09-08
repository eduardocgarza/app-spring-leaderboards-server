<?php

namespace Tests\Unit;

use Illuminate\Http\Response;
use Tests\TestCase;

class CreateUserTests extends TestCase
{
    private $createRoute;

    public function setUp(): void
    {
        parent::setUp();
        $this->createRoute = generateEndpoint("/users");
    }

    /**
     * Test creating a user with an empty body.
     * It: Sends a request to create a user with an empty body.
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithEmptyBody(): void
    {
        $response = $this->post($this->createRoute, []);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a user with an empty first name.
     * It: Sends a request to create a user with an empty first name.
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithEmptyFirstName(): void
    {
        $response = $this->post($this->createRoute, ['firstName' => '']);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a user with firstName length greater than 50.
     * It: Sends a request to create a user with a first name longer than 50 characters.
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithLongFirstName(): void
    {
        // Create a string longer than 50 characters
        $longName = str_repeat('a', 51);

        $response = $this->post($this->createRoute, ['firstName' => $longName]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a valid user.
     * It: Sends a request to create a valid user.
     * Expect: User creation succeeds (201 Created).
     *         User JSON response structure is validated.
     */
    public function testCreateValidUser(): void
    {
        $response = $this->post($this->createRoute, ['firstName' => 'John']);
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'userID',
                'username',
                'firstName',
                'points',
            ]);

        // Delete the user
        $id = $response->json('userID');
        $deleteRoute = generateEndpoint("/users/{$id}");
        $this->delete($deleteRoute);
    }

    /**
     * Test creating a user with an empty dateBirth.
     * It: Sends a request to create a user with an empty dateBirth.
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithEmptyDateBirth(): void
    {
        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'dateBirth' => '',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a user with an invalid dateBirth (random string).
     * It: Sends a request to create a user with an invalid dateBirth (random string).
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithInvalidDateBirth(): void
    {
        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'dateBirth' => 'random_string',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a user with an invalid dateBirth ('2020-05', no day).
     * It: Sends a request to create a user with an invalid dateBirth ('2020-05', no day).
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithInvalidDateBirthNoDay(): void
    {
        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'dateBirth' => '2020-05',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a valid user with dateBirth (Y-m-d).
     * It: Sends a request to create a valid user with dateBirth (Y-m-d).
     * Expect: User creation succeeds (201 Created).
     *         User JSON response structure is validated.
     */
    public function testCreateValidUserWithDateBirth(): void
    {
        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'dateBirth' => '1990-01-15',
        ]);
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'userID',
                'username',
                'firstName',
                'points',
                'dateBirth',
                'address',
            ]);

        // Delete the user
        $id = $response->json('userID');
        $deleteRoute = generateEndpoint("/users/{$id}");
        $this->delete($deleteRoute);
    }

    /**
     * Test creating a user with an empty address.
     * It: Sends a request to create a user with an empty address.
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithEmptyAddress(): void
    {
        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'address' => '',
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a user with an invalid address (number, not string).
     * It: Sends a request to create a user with an invalid address (number, not string).
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithInvalidAddress(): void
    {
        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'address' => 12345,
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a user with an invalid address (length > 255).
     * It: Sends a request to create a user with an invalid address (length > 255).
     * Expect: User creation fails (400 Bad Request).
     */
    public function testCreateUserWithInvalidLongAddress(): void
    {
        $longAddress = str_repeat('a', 256);

        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'address' => $longAddress,
        ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test creating a valid user with an address.
     * It: Sends a request to create a valid user with an address.
     * Expect: User creation succeeds (201 Created).
     *         User JSON response structure is validated.
     */
    public function testCreateValidUserWithAddress(): void
    {
        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'address' => '123 Main St',
        ]);
        $response->assertStatus(Response::
        HTTP_CREATED)
            ->assertJsonStructure([
                'userID',
                'username',
                'firstName',
                'points',
                'dateBirth',
                'address',
            ]);

        // Delete the user
        $id = $response->json('userID');
        $deleteRoute = generateEndpoint("/users/{$id}");
        $this->delete($deleteRoute);
    }

    /**
     * Test creating a valid user with dateBirth and address.
     * It: Sends a request to create a valid user with dateBirth and address.
     * Expect: User creation succeeds (201 Created).
     *         User JSON response structure is validated.
     */
    public function testCreateValidUserWithDateBirthAndAddress(): void
    {
        $response = $this->post($this->createRoute, [
            'firstName' => 'John',
            'dateBirth' => '1990-01-15',
            'address' => '123 Main St',
        ]);
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'userID',
                'username',
                'firstName',
                'points',
                'dateBirth',
                'address',
            ]);

        // Delete the user
        $id = $response->json('userID');
        $deleteRoute = generateEndpoint("/users/{$id}");
        $this->delete($deleteRoute);
    }
}
