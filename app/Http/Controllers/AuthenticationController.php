<?php

namespace App\Http\Controllers;

use App\Events\UserLoggedInEvent;
use App\Models\User;
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
            logger('Failed to login?');
            return response()->json('Invalid credentials');
        }

        event(new UserLoggedInEvent(Auth::user()));
        return response()->json('success');
    }

    public function logout()
    {
        Auth::logout();

        return response()->json('success');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')]);

        return response()->json(['message' => 'User registered successfully.']);
    }
}
