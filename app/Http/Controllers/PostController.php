<?php

namespace App\Http\Controllers;

use App\Models\Post;

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
}
