<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = "roles";
    protected $fillable = ['name', 'slug', 'description'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
