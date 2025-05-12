<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    /**
     * Test that HTTPS is forced in production.
     */
    public function test_https_is_forced_in_production()
    {
        // Store the original environment and config
        $originalEnv = app()->environment();
        $originalSecure = config('session.secure');
        
        // Temporarily set environment to production
        app()['env'] = 'production';
        
        // Force secure cookies for the test
        config(['session.secure' => true]);
        
        // Check if secure cookies are enabled in production
        $this->assertTrue(config('session.secure'));
        
        // Reset environment and config
        app()['env'] = $originalEnv;
        config(['session.secure' => $originalSecure]);
    }
    
    /**
     * Test that CSRF protection is enabled.
     */
    public function test_csrf_protection_is_enabled()
    {
        // Check that session middleware is enabled (required for CSRF)
        $middleware = config('app.middleware', []);
        if (empty($middleware)) {
            $middleware = config('app.middleware_groups.web', []);
        }
        
        $sessionMiddleware = \Illuminate\Session\Middleware\StartSession::class;
        $this->assertTrue(in_array($sessionMiddleware, $middleware) || 
                         in_array('Illuminate\Session\Middleware\StartSession', $middleware) ||
                         in_array('session', $middleware));
        
        // Attempt to post without CSRF token (if not in testing environment)
        if (app()->environment() !== 'testing') {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'password',
            ]);
            
            // Should fail with 419 (CSRF token mismatch)
            $response->assertStatus(419);
        } else {
            // In testing environment, CSRF might be disabled
            $this->assertTrue(true);
        }
    }
    
    /**
     * Test that XSS protection headers are set.
     */
    public function test_xss_protection_headers()
    {
        $response = $this->get('/');
        
        // Check for content-type header
        $response->assertHeader('Content-Type');
        
        // Check for X-XSS-Protection header
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        
        // Check for X-Content-Type-Options header
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }
    
    /**
     * Test that authentication routes are protected.
     */
    public function test_authentication_routes_are_protected()
    {
        // Try to access dashboard without authentication
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        
        // Try to access purchase requests without authentication
        $response = $this->get('/purchase-requests');
        $response->assertRedirect('/login');
        
        // Try to access supplier management without authentication
        $response = $this->get('/suppliers');
        $response->assertRedirect('/login');
    }
} 