<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_welcome_page_shows_only_active_courses(): void
    {
        // Create an active course
        $activeCourse = \App\Models\Course::create([
            'name' => 'Active Course Test',
            'hours' => 20,
            'status' => 'ativo',
            'active' => true,
        ]);

        // Create an inactive course
        $inactiveCourse = \App\Models\Course::create([
            'name' => 'Inactive Course Test',
            'hours' => 20,
            'status' => 'ativo',
            'active' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Active Course Test');
        $response->assertDontSee('Inactive Course Test');
    }
}
