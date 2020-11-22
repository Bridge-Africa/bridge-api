<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->jobTitle,
            'description' => $this->faker->realText(),
            'price' => $this->faker->randomNumber(),
            'image' => $this->faker->imageUrl(),
            'thumbnail' => $this->faker->imageUrl(),
            'user_id' => $this->faker->randomElement(range(1,10)),
            'created_at' => $this->faker->dateTimeBetween('-3months'),
            'updated_at' => $this->faker->dateTimeBetween('-3months')
        ];
    }
}
