<?php

namespace App\Http\Controllers;

use App\Events\UserLoggedInEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        event(new UserLoggedInEvent(auth()->user()));
        return response()->json('success');
    }

    public function logout()
    {
        Auth::logout();

        return response()->json('success');
    }
}
