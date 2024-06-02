<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UsersController extends Controller
{
    public function show(Request $request, User $user)
    {
        $user->isFollowing = Auth::user()->following()->find($user->id) ? true : false;

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

        $searchResult = collect(json_decode($response->body(), true));
        $userUUIDs = $searchResult->pluck('uuid')->all();

        $users = User::whereIn('uuid', $userUUIDs)
            ->orderByRaw(DB::raw("FIELD(uuid, '" . implode("','", $userUUIDs) . "')"))
            ->get();

        return response()->json($users);
    }

    public function follow(Request $request, User $user)
    {
        // can't follow yourself
        if ($user->id === Auth::user()->id) {
            return response()->json(['message' => 'You cannot follow or unfollow yourself.'], 400);
        }

        // check if auth is following this user already, if so unfollow otherwise follow
        if(Auth::user()->following()->find($user->id)) {
            (Auth::user())->following()->detach($user);

            return response()->json(['message' => 'You have unfollowed this user.']);
        }

        (Auth::user())->following()->attach($user);

        return response()->json(['message' => 'You are now following this user.']);
    }

    public function followers(Request $request)
    {
        $user = Auth::user();
        $followers = $user->followers()->get();

        return response()->json($followers);
    }

    public function following(Request $request)
    {
        $user = Auth::user();
        $following = $user->following()->get();

        return response()->json($following);
    }
}
