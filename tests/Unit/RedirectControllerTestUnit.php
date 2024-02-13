<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Redirect;
use App\Services\HashidsService;

class RedirectControllerTestUnit extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_redirects()
    {
        $redirect = Redirect::factory()->create();

        $response = $this->getJson('/api/redirects');

        $response->assertOk()
                ->assertJson([$redirect->toArray()]);
    }

    public function test_store_creates_new_redirect()
    {
        $data = [
            'destination_url' => 'https://example.com',
        ];

        $response = $this->postJson('/api/redirects', $data);

        $response->assertCreated()
                ->assertJsonFragment(['message' => 'Redirect created successfully']);
        $this->assertDatabaseHas('redirects', ['destination_url' => 'https://example.com']);
    }

    public function test_show_displays_redirect()
    {
        $redirect = Redirect::factory()->create();
        $code = app(HashidsService::class)->encode($redirect->id);

        $response = $this->getJson("/api/redirects/{$code}");

        $response->assertOk()
                ->assertJson($redirect->toArray());
    }

    public function test_update_updates_redirect()
    {
        $redirect = Redirect::factory()->create(['destination_url' => 'https://example.com']);
        $code = app(HashidsService::class)->encode($redirect->id);

        $newData = ['destination_url' => 'https://newexample.com'];

        $response = $this->putJson("/api/redirects/{$code}", $newData);

        $response->assertOk()
                 ->assertJson(['message' => 'Redirect updated successfully']);
        $this->assertDatabaseHas('redirects', ['destination_url' => 'https://newexample.com']);
    }

    public function test_destroy_deletes_redirect()
    {
        $redirect = Redirect::factory()->create();
        $code = app(HashidsService::class)->encode($redirect->id);

        $response = $this->deleteJson("/api/redirects/{$code}");

        $response->assertOk()
                ->assertJson(['message' => 'Redirect deleted successfully']);
        $this->assertSoftDeleted($redirect);
    }

    public function test_redirect_to_destination_redirects_correctly()
    {
        $redirect = Redirect::factory()->create([
            'destination_url' => 'https://otimize.me/',
            'active' => true,
        ]);
        $code = app(HashidsService::class)->encode($redirect->id);

        $response = $this->get("/r/{$code}");

        $response->assertRedirect('https://otimize.me/');
    }
}
