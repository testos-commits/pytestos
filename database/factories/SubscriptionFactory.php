<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'subscription',
            'price_text' => '100USD',
            'price' => 100,
            'is_free' => false,
            'features' => [
                'feature1',
                'feature2',
                'feature3',
            ]
        ];
    }
}
