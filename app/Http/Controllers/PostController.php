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
    public function index()
    {
        $user = auth()->user();
        $user_following = $user->following()->pluck('users.id');

        $user_following->push($user->id);

        $posts = Post::whereIn('user_id', $user_following)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
        return $post->load('comments');
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
