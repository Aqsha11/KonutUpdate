<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoryFactory extends Factory
{
    protected $model = Story::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'category_id' => Category::factory(),
            'image' => 'stories/'.fake()->uuid().'.webp',
            'url' => fake()->boolean(50) ? fake()->url() : null,
            'is_active' => true,
            'expires_at' => now()->addDays(Story::EXPIRE_DAYS),
            'sort_order' => 0,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
