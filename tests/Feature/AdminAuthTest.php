<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_path_redirects_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard_after_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(302); // Redirige a /dashboard
        $response->assertRedirect('/dashboard');
        
        $response2 = $this->actingAs($user)->get('/dashboard');
        $response2->assertStatus(200);
        $response2->assertSee('DAWSCAPE');
    }
}
