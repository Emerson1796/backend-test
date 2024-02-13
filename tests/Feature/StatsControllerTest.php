<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Redirect;
use App\Models\RedirectLog;
use App\Services\HashidsService;

class StatsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_stats_for_redirect()
    {
        $redirect = Redirect::factory()->create();
        $logs = RedirectLog::factory()->count(10)->create([
            'redirect_id' => $redirect->id,
            'ip' => '123.456.789.101',
            'referer' => 'https://example.com',
            'created_at' => now()->subDays(5),
        ]);

        $service = new HashidsService();
        $code = $service->encode($redirect->id);

        $response = $this->getJson("/api/redirects/{$code}/stats");

        $response->assertOk()
                 ->assertJson([
                     'total_accesses' => 10,
                     'unique_accesses' => 1,
                 ]);
    }

    public function test_show_logs_for_redirect()
    {
        $redirect = Redirect::factory()->create();
        $logs = RedirectLog::factory()->count(5)->create(['redirect_id' => $redirect->id]);

        $service = new HashidsService();
        $code = $service->encode($redirect->id);

        $response = $this->getJson("/api/redirects/{$code}/logs");

        $response->assertOk()
                ->assertJsonCount(5)
                ->assertJson($logs->toArray());
    }
}
