<?php

namespace App\Http\Controllers\Driver\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssignController extends Controller
{
    public function show(){
        return view('driver.dashboard');
    }
}
