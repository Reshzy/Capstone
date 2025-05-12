<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company() . ' Department',
            'code' => function (array $attributes) {
                // Generate a code from the first letters of each word in the name
                $words = explode(' ', $attributes['name']);
                $code = '';
                foreach ($words as $word) {
                    $code .= strtoupper(substr($word, 0, 1));
                }
                return $code;
            },
            'description' => fake()->sentence(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
} 