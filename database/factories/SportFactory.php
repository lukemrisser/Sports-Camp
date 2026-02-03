<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sport>
 */
class SportFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'Sport_Name' => $this->faker->word(),
        ];
    }
}
