<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $message = $this->faker->sentence;
        return [
            'audition_id' => 1,
            'message' => $message,
            'message_ar' => $message . '_ar',
            'for_applicants' => 0,
            'for_shortlisted' => 0,
            'for_accepted' => 0,
        ];
    }
}
