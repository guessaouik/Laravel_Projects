<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public static array $previousIds = [];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (count(static::$previousIds) > 0){
            $randomId = static::$previousIds[array_rand(static::$previousIds)];
        } else $randomId = 0;
        return [
            'parent_id' => $randomId,
            'title' => $this->faker->text(50),
            'content' => $this->faker->text(250)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Post $post){
            static::$previousIds[] = $post->post_id;
        });
    }
}
