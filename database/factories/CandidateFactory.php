<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CandidateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'document_number' => $this->faker->numerify('##########'),
            'access_code' => strtoupper(Str::random(8)),
            'position_id' => Position::factory(),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'completed']),
            'created_by' => User::factory(),
        ];
    }
}
