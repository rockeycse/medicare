<?php

use App\Models\User;

test('user can register', function () {
    $this->postJson('/api/v1/auth/register', [
        'name'                  => 'John Doe',
        'email'                 => 'john@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])
    ->assertStatus(201)
    ->assertJsonStructure([
        'success', 'data' => ['user', 'token']
    ]);
});

test('user can login', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $this->postJson('/api/v1/auth/login', [
        'email'    => $user->email,
        'password' => 'password',
    ])
    ->assertOk()
    ->assertJsonPath('success', true)
    ->assertJsonStructure(['data' => ['token']]);
});

test('invalid credentials return 422', function () {
    $this->postJson('/api/v1/auth/login', [
        'email'    => 'wrong@email.com',
        'password' => 'wrongpassword',
    ])->assertStatus(422);
});
