<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UsersController extends Controller
{
    public function show(Request $request, User $user)
    {
        return response()->json($user);
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|string'
        ]);

        $searchServiceHost = config('services.social-search.host');

        // make HTTP post call to search service
        $response = Http::post($searchServiceHost . '/api/users/search', [
            'value' => $request->value
        ]);

        return response()->json(json_decode($response->body()));
    }
}
