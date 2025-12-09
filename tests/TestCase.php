<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run seeders for each test
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    /**
     * Create authenticated user with specific role.
     */
    protected function authenticateAs(string $role = 'Super Admin'): \App\Models\User
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user, 'sanctum');
        
        return $user;
    }

    /**
     * Create user with specific role without authentication.
     */
    protected function createUserWithRole(string $role): \App\Models\User
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole($role);
        
        return $user;
    }

    /**
     * Assert JSON response has validation errors for specific fields.
     */
    protected function assertValidationErrors(array $fields): void
    {
        foreach ($fields as $field) {
            $this->assertArrayHasKey($field, $this->json['errors'] ?? []);
        }
    }
}
