<?php

use App\Models\User;
use Laravel\Passport\Passport;

test('admin can list all users', function () {
    User::factory()->count(5)->create();
    $admin = User::factory()->admin()->create();

    Passport::actingAs($admin);

    $this->getJson('/api/v1/admin/users')
         ->assertOk()
         ->assertJsonPath('success', true);
});

test('admin can toggle user status', function () {
    $admin = User::factory()->admin()->create();
    $user  = User::factory()->create(['is_active' => true]);

    Passport::actingAs($admin);

    $this->patchJson("/api/v1/admin/users/{$user->id}/toggle-status")
         ->assertOk()
         ->assertJsonPath('data.is_active', false);
});

test('non-admin cannot access user management', function () {
    $patient = User::factory()->create(['role' => 'patient']);

    Passport::actingAs($patient);

    $this->getJson('/api/v1/admin/users')
         ->assertStatus(403);
});
