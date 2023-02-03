<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        return response()->json($user);
    }
}
