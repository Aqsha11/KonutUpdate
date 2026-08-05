<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(3, true),
            'thumbnail' => fake()->imageUrl(),
            'status' => 'draft',
            'is_breaking' => false,
            'is_headline' => false,
            'published_at' => null,
            'views_count' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function headline(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_headline' => true,
            'headline_expires_at' => now()->addDays(7),
        ]);
    }

    public function breaking(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_breaking' => true,
            'breaking_expires_at' => now()->addDays(7),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_headline' => true,
            'headline_expires_at' => now()->subDay(),
            'is_breaking' => true,
            'breaking_expires_at' => now()->subDay(),
        ]);
    }
}
