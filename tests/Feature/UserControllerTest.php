<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as FakerFactory;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = FakerFactory::create();
    }

    public function auth($user): string
    {
        $response = $this->postJson("/api/users/auth/", [
            "email" => $user->email,
            "password" => 'password',
            'token_name' => 'tests',
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['success' => true]);
        return $response->json('data');
    }

    /** @test */
    public function it_can_create_user_without_details()
    {
        $data = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['success' => true])
            ->assertJsonFragment(['first_name' => $data['first_name']])
            ->assertJsonFragment(['last_name' => $data['last_name']])
            ->assertJsonFragment(['email' => $data['email']]);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);
    }

    /** @test */
    public function it_can_create_user_with_details()
    {
        $data = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'address' => $this->faker->address,
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['success' => true])
            ->assertJsonFragment(['first_name' => $data['first_name']])
            ->assertJsonFragment(['last_name' => $data['last_name']])
            ->assertJsonFragment(['email' => $data['email']])
            ->assertJsonFragment(['address' => $data['address']]);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);
        $this->assertDatabaseHas('user_details', ['address' => $data['address']]);
    }

    /** @test */
    public function it_deletes_user_details()
    {
        $address = $this->faker->address;
        $user = User::factory()->withAddress($address)->create();
        $token = $this->auth($user);

        $data = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'updated',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(["success" => true])
            ->assertJsonFragment(["first_name" => $data["first_name"]])
            ->assertJsonFragment(["last_name" => $data["last_name"]])
            ->assertJsonFragment(["email" => $data["email"]]);

        $this->assertDatabaseMissing('user_details', ['user_id' => $user->id]);
    }

    /** @test */
    public function it_can_update_user_with_details()
    {
        $address = $this->faker->address;
        $oldEmail = $this->faker->unique()->safeEmail;
        $user = User::factory()->withAddress($address)->create(['email' => $oldEmail]);
        $token = $this->auth($user);

        $data = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'updated',
            'address' => $this->faker->address,
        ];

        $response = $this->putJson("/api/users/{$user->id}", $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['success' => true])
            ->assertJsonFragment(['first_name' => $data['first_name']])
            ->assertJsonFragment(['address' => $data['address']]);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);
        $this->assertDatabaseHas('user_details', ['address' => $data['address']]);
        $this->assertDatabaseMissing('users', ['email' => $oldEmail]);
    }

    /** @test */
    public function it_can_delete_user_with_details()
    {
        $user = User::factory()->withAddress('address')->create();
        $token = $this->auth($user);

        $response = $this->deleteJson("/api/users/{$user->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['success' => true]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('user_details', ['user_id' => $user->id]);
    }

    /** @test */
    public function it_can_list_all_users_with_details()
    {
        $USER_COUNT = 3;
        User::factory($USER_COUNT)->withAddress('address')->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['success' => true])
            ->assertJsonCount($USER_COUNT, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'address',
                    ],
                ],
            ]);
    }
}
