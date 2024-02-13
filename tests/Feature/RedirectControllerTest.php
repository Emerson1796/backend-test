<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Redirect;
use App\Services\HashidsService;

class RedirectControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function redirects_can_be_listed()
    {
        $redirect = Redirect::factory()->create();

        $response = $this->get('/api/redirects');

        $response->assertOk()
                 ->assertJsonFragment([
                     'destination_url' => $redirect->destination_url
                 ]);
    }

    /** @test */
    public function a_redirect_can_be_created()
    {
        $response = $this->postJson('/api/redirects', [
            'destination_url' => 'https://example.com',
        ]);

        $response->assertCreated()
                 ->assertJsonFragment([
                     'message' => 'Redirect created successfully',
                 ]);

        $this->assertDatabaseHas('redirects', [
            'destination_url' => 'https://example.com',
        ]);
    }

    /** @test */
    public function a_redirect_can_be_shown()
    {
        $redirect = Redirect::factory()->create();
        $service = new HashidsService();
        $code = $service->encode($redirect->id);

        $response = $this->get("/api/redirects/{$code}");

        $response->assertOk()
                 ->assertJsonFragment([
                     'destination_url' => $redirect->destination_url
                 ]);
    }

    /** @test */
    public function a_redirect_can_be_updated()
    {
        $redirect = Redirect::factory()->create();
        $service = new HashidsService();
        $code = $service->encode($redirect->id);

        $response = $this->putJson("/api/redirects/{$code}", [
            'destination_url' => 'https://updatedexample.com',
        ]);

        $response->assertOk()
                 ->assertJson(['message' => 'Redirect updated successfully']);

        $this->assertDatabaseHas('redirects', [
            'id' => $redirect->id,
            'destination_url' => 'https://updatedexample.com',
        ]);
    }

    /** @test */
    public function a_redirect_can_be_deleted()
    {
        $redirect = Redirect::factory()->create();
        $service = new HashidsService();
        $code = $service->encode($redirect->id);

        $response = $this->delete("/api/redirects/{$code}");

        $response->assertOk()
                 ->assertJson(['message' => 'Redirect deleted successfully']);

        $this->assertSoftDeleted($redirect);
    }

    /** @test */
    public function a_redirect_can_redirect_to_destination()
    {
        $redirect = Redirect::factory()->create([
            'destination_url' => 'https://otimize.me/',
            'active' => true,
        ]);

        $service = new HashidsService();
        $code = $service->encode($redirect->id);

        $response = $this->get("/r/{$code}");

        $response->assertRedirect('https://otimize.me/');
    }
}
