<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = "tickets";
    protected $fillable = ["ticket_number", "location_id", "reference", "container_qty", "ticket_date", "customer_id", "amount", "status", "created_by"];
}
