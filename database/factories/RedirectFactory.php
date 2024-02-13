<?php

namespace Database\Factories;

use App\Models\Redirect;
use Illuminate\Database\Eloquent\Factories\Factory;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Redirect>
 */
class RedirectFactory extends Factory
{
    protected $model = Redirect::class;


    public function definition()
    {
        $fakeId = rand(1000, 9999);
        return [
            'destination_url' => $this->faker->url,
            'active' => true,
            'code' => Hashids::encode($fakeId),
        ];
    }
}
