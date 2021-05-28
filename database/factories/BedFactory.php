<?php

namespace Database\Factories;

use App\Models\Bed;
use Illuminate\Database\Eloquent\Factories\Factory;

class BedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bed::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        return [
            'category_id' => random_int(1,3),
            'hospital_id' => $this->faker->randomElement([
                1,3,4
            ]),
            'day_cost' => $this->faker->randomFloat(2,1,5000),
            'type_id' => 1
        ];
    }
}
