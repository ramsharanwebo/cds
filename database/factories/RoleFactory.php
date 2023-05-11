<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        
        $roles = ['Super Admin', 'Site Manager', 'Operator', 'Accountant'];
        return [
            'name' => $roles[array_rand($roles)],
            'slug' => Str::slug($roles[array_rand($roles)]),
            'description' => Str::random(10),
        ];
    }
}
