<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Audition>
 */
class AuditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => 1,
            'title' => $this->translations('en', $this->faker->sentence),
            'description' => $this->translations('en', $this->faker->paragraph),
            'image' => $this->translations('en', 'https://picsum.photos/200/300'),
            'location' => $this->translations('en', $this->faker->address),
            'filming_date' => $this->faker->date(),
            'applications_deadline' => "2054-09-19",
            'filming_budget' => $this->faker->randomFloat(),
            'usage_budget' => $this->faker->randomFloat(),
            'currency' => $this->faker->currencyCode,
            'show_budget' => $this->faker->boolean(80), 
            'duration' => $this->translations('en', $this->faker->randomDigit . ' days'),
            'min_age' => 25,
            'max_age' => 30,
            'gender' => 'male'
        ];
    }
}
