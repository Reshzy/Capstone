<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseRequest>
 */
class PurchaseRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department' => fake()->company() . ' Department',
            'pr_number' => 'PR-' . date('Y-m') . '-' . fake()->unique()->randomNumber(4, true),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'estimated_amount' => fake()->randomFloat(2, 1000, 50000),
            'status' => fake()->randomElement(['draft', 'submitted', 'approved', 'rejected']),
            'document_path' => null,
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
            'approver_id' => function (array $attributes) {
                return ($attributes['status'] === 'approved' || $attributes['status'] === 'rejected') ? User::factory() : null;
            },
            'rejection_reason' => function (array $attributes) {
                return $attributes['status'] === 'rejected' ? fake()->sentence() : null;
            },
        ];
    }
    
    /**
     * Indicate that the PR is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'approver_id' => null,
            'rejection_reason' => null,
        ]);
    }
    
    /**
     * Indicate that the PR is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'approver_id' => null,
            'rejection_reason' => null,
        ]);
    }
    
    /**
     * Indicate that the PR is approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approver_id' => User::factory(),
                'rejection_reason' => null,
            ];
        });
    }
    
    /**
     * Indicate that the PR is rejected.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'approver_id' => User::factory(),
                'rejection_reason' => fake()->sentence(),
            ];
        });
    }
} 