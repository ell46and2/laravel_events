<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function show()
    {
    	$users = User::get();

    	return view('admin.index',['users' => $users]);

    }
}
