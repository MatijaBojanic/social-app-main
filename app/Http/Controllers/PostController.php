<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Retrieves the posts for the authenticated user and the users they are following.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100'
        ]);


        $user = auth()->user();
        $followedUsers = $user->following()->pluck('users.id');

        $followedUsers->push($user->id);

        $posts = Post::whereIn('user_id', $followedUsers)
            ->orderBy('created_at', 'desc')
            ->with(['user'])
            ->paginate($request->per_page ?? 10, ['*'], 'page', $request->page ?? 1);

        return $posts;
    }

    /**
     * Show the comments for a given post.
     *
     * @param Post $post The post for which to display the comments.
     *
     * @return Post
     */
    public function show(Post $post)
    {
        return $post->load(['user', 'comments.user']);
    }

    /**
     * Store a newly created post in storage.
     *
     * @return Post
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'body' => 'required|max:500',
        ]);

        $post = Post::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return $post;
    }
}
