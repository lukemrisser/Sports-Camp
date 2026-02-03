<?php

namespace Database\Factories;

use App\Models\Camp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Camp>
 */
class CampFactory extends Factory
{
    // The model this factory is for
    protected $model = Camp::class;

    public function definition() {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+2 months');

        return [
            // Matches Camp_Name varchar(50)
            'Camp_Name' => $this->faker->words(3, true) . ' Basketball Camp',

            // Matches Description test
            'Description' => $this->faker->paragraph(1),

            // Matches Start_Date date
            'Start_Date' => $startDate->format('Y-m-d'),

            // Matches End_Date date
            'End_Date' => $endDate->format('Y-m-d'),

            // Matches Registration_Open date
            'Registration_Open' => $this->faker->dateTimeBetween('-1 month', $startDate)->format('Y-m-d'),

            // Matches Registration_Close date
           'Registration_Close' => $this->faker->dateTimeBetween($startDate, $endDate)->format('Y-m-d'),
           
           // Matches Price decimal (10, 2)
           'Price' => $this->faker->randomFloat(2, 50, 500),

           // Matches Camp_Gender enum('girls', 'boys', 'mixed')
           'Camp_Gender' => $this->faker->randomElement(['boys', 'girls', 'mixed']),

           // Matches Age_Min tinyint(4)
           'Age_Min' => $this->faker->numberBetween(6, 10),

           // Matches Age_Max tinyint(4)
           'Age_Max' => $this->faker->numberBetween(11, 18),
        ];
    }
}