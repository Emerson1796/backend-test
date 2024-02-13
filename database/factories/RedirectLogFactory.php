<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RedirectLog>
 */
class RedirectLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'redirect_id' => \App\Models\Redirect::factory(),
            'ip' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'referer' => $this->faker->url,
            'query_params' => '{}',
        ];
    }
}
