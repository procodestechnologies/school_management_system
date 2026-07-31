<?php

namespace Modules\Student\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentDetailsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Student\Models\StudentDetails::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

