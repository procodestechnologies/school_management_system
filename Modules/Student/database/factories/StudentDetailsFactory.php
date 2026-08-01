<?php

namespace Modules\Student\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Student\Models\StudentDetails;

class StudentDetailsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = StudentDetails::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
