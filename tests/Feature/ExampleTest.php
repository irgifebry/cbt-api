<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_root_path_does_not_serve_a_web_page(): void
    {
        $response = $this->get('/');

        $response->assertNotFound();
    }
}
