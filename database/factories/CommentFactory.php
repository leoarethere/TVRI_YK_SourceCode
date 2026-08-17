<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'body' => fake()->paragraph(),
            'status' => 'pending',
            'commentable_id' => 1,
            'commentable_type' => News::class,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}
