<?php

use App\Models\User;

test('admin can list all users', function () {
    User::factory()->count(5)->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'api')
         ->getJson('/api/v1/admin/users')
         ->assertOk()
         ->assertJsonPath('success', true);
});

test('admin can toggle user status', function () {
    $admin = User::factory()->admin()->create();
    $user  = User::factory()->create(['is_active' => true]);

    $this->actingAs($admin, 'api')
         ->patchJson("/api/v1/admin/users/{$user->id}/toggle-status")
         ->assertOk()
         ->assertJsonPath('data.is_active', false);
});

test('non-admin cannot access user management', function () {
    $patient = User::factory()->create(['role' => 'patient']);

    $this->actingAs($patient, 'api')
         ->getJson('/api/v1/admin/users')
         ->assertStatus(403);
});
