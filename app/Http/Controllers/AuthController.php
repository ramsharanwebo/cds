<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    //
    public function login()
    {
        return "Google";
    }

    public function callback()
    {
        return "Hello there";
    }

    public function logout(Request $request)
    {
        return "Hello Log out!";
    }
}
