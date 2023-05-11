<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueEmailIgnoreCurrentUser implements Rule
{
    protected $currentUserId;

    public function __construct($currentUserId)
    {
        $this->currentUserId = $currentUserId;
    }

    public function passes($attribute, $value)
    {
        return DB::table('users')
            ->where('email', $value)
            ->where('id', '!=', $this->currentUserId)
            ->doesntExist();
    }

    public function message()
    {
        return 'The email has already been taken.';
    }
}
