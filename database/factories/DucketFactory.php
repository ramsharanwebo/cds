<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ducket>
 */
class DucketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userIds = User::inRandomOrder()->pluck('id')->toArray();
        $date = Carbon::now();
        
        return [
            "identity"=>$date->toDateString().'-'.fake()->unique()->numberBetween($min = 10000, $max = 500000).'-'.Str::random(6), 
            // "ticket_id"=>"", 
            "ducket_date"=> $date->toDateString(), 
            "goods"=>fake()->sentence(2), 
            "notes"=>fake()->sentence(5), 
            "gst"=>fake()->randomFloat(2), 
            "levy"=>"", 
            "total_amount"=>fake()->randomFloat(2), 
            "count"=>fake()->numberBetween($min = 10, $max = 100).'-'.Str::random(6), 
            "created_by"=>$userIds[0],
        ];
    }
}
