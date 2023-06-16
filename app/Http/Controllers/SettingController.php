<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getSystemSettings(Request $request){
        return "hello there";
    }
}
