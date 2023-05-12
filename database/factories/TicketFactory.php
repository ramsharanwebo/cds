<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = User::inRandomOrder()->pluck('id')->toArray();
        $customers = Customer::inRandomOrder()->pluck('id')->toArray();
        $locations = Location::inRandomOrder()->pluck('id')->toArray();
        $date = Carbon::now();
        
        return [
            "ticket_number"=>fake()->unique()->numberBetween($min = 10000, $max = 500000), 
            "location_id"=>$locations[0], 
            "customer_id"=>$customers[0], 
            "ticket_date"=> $date->toDateString(), 
            "reference"=>fake()->sentence(2),
            "amount"=>fake()->randomFloat(2), 
            "container_qty"=>fake()->numberBetween($min = 10, $max = 100), 
            "created_by"=>$userIds[0],
            'status' => fake()->boolean(),
        
        ];
    }
}
