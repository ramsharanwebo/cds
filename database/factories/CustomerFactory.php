<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $business_models = ['business', 'individual'];

        $randomIndex = array_rand($business_models);
        $randomString = $business_models[$randomIndex];

        $states = ["Alaska", "Alabama", "Arkansas", "American Samoa", "Arizona", "California", "Colorado", "Connecticut", "District of Columbia", "Delaware", "Florida", "Georgia", "Guam", "Hawaii", "Iowa", "Idaho", "Illinois", "Indiana", "Kansas", "Kentucky", "Louisiana", "Massachusetts", "Maryland", "Maine", "Michigan", "Minnesota", "Missouri", "Mississippi", "Montana", "North Carolina", "North Dakota", "Nebraska", "New Hampshire", "New Jersey", "New Mexico", "Nevada", "New York", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Puerto Rico", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Virginia", "Virgin Islands", "Vermont", "Washington", "Wisconsin", "West Virginia", "Wyoming"];
        $userIds = User::inRandomOrder()->pluck('id')->toArray();

        return [
            'business_model_type' => $randomString,
            'abn_number' => fake()->unique()->numberBetween($min = 10000, $max = 500000),
            'business_name' => fake()->word(3),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'name' => fake()->name,
            'contact_number' => fake()->phoneNumber(),
            'suburb' => fake()->city(),
            'state' => $this->faker->randomElement($states),
            'postal_code' => fake()->postcode,
            'transaction_summary_perference' => fake()->sentence(2),
            'created_by' => $userIds[0]
        ];
    }
}
