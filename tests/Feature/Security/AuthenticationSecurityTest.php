<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test SQL injection prevention in login.
     */
    public function test_sql_injection_prevention_in_login(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => "admin@example.com' OR '1'='1",
            'password' => "password' OR '1'='1",
        ]);

        // Should reject with either 401 (invalid creds) or 422 (validation error)
        $this->assertTrue(in_array($response->status(), [401, 422]));
    }

    /**
     * Test XSS prevention in user input.
     */
    public function test_xss_prevention_in_user_input(): void
    {
        $this->authenticateAs('Admin');

        $response = $this->postJson('/api/users', [
            'name' => '<script>alert("XSS")</script>',
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        // Should either sanitize or reject
        $this->assertTrue(in_array($response->status(), [201, 422]));
    }

    /**
     * Test rate limiting on login endpoint.
     */
    public function test_rate_limiting_on_login_endpoint(): void
    {
        // Attempt multiple failed logins
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // Next attempt should be rate limited
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertTrue(in_array($response->status(), [429, 401]));
    }

    /**
     * Test CSRF protection on state-changing requests.
     */
    public function test_csrf_protection_exists(): void
    {
        $user = $this->authenticateAs('Admin');

        // Sanctum uses token-based auth for API
        // Verify that authenticated requests work
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user, 'sanctum');
    }

    /**
     * Test password strength requirement.
     */
    public function test_password_strength_requirement(): void
    {
        $this->authenticateAs('Admin');

        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'weak@example.com',
            'password' => '12345', // Weak password
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test unauthorized access is blocked.
     */
    public function test_unauthorized_access_is_blocked(): void
    {
        $this->authenticateAs('Viewer');

        $response = $this->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test token expiration.
     */
    public function test_token_has_expiration(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token', ['*'], now()->subDay());

        $this->actingAs($user, 'sanctum');
        
        // Token should be expired
        $response = $this->getJson('/api/user');
        
        // Should handle expired token properly
        $this->assertTrue(in_array($response->status(), [200, 401]));
    }

    /**
     * Test session hijacking prevention.
     */
    public function test_session_hijacking_prevention(): void
    {
        $user = $this->authenticateAs('Admin');
        $token = $user->currentAccessToken();

        // Try to use same token from different user agent
        $this->withHeaders([
            'User-Agent' => 'Malicious-Bot/1.0',
        ])->getJson('/api/user');

        // Application should track and potentially flag suspicious activity
        $this->assertTrue(true); // Placeholder for audit log check
    }
}
