<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = ['business_model_type', 'business_model','abn_number', 'business_name', 'phone', 'name', 'contact_number', 
        'suburb', 'state', 'postal_code', 'email', 'transaction_summary_perference', 'created_by'];
}
