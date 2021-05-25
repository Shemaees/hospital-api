<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HospitalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hospital::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'branch' => $this->faker->text,
        ];
    }
    public function configure()
    {
        return $this->afterMaking(function (Hospital $hospital) {
            //
        })->afterCreating(function (Hospital $hospital) {
            $hospital->categories()->attach(Category::all());
        });
    }
}
