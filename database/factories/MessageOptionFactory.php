<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessageOption>
 */
class MessageOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $option = $this->faker->sentence;
        return [
            'audition_message_id' => 1,
            'option' => $option,
            'option_ar' => $option . '_ar',
        ];
    }
}
