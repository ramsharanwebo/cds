<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ducket extends Model
{
    use HasFactory;
    protected $table = "duckets";

    protected $fillable = ["identity", 
        // "ticket_id", 
        "ducket_date", "goods", "notes", "gst", "levy", "total_amount", "count", "created_by"];
}
