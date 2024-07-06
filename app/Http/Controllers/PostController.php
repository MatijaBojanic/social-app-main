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
            'per_page' => 'integer|min:1|max:100',
            'user_id' => 'integer|exists:users,id',
        ]);

        if($request->user_id) {
            $postOwners = collect([$request->user_id]);
        } else {
            $user = auth()->user();
            $postOwners = $user->following()->pluck('users.id');

            $postOwners->push($user->id);
        }

        $posts = Post::whereIn('user_id', $postOwners)
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
        return $post->load([
            'user',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            }
        ]);    }

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
            'image' => 'image|max:2048'
        ]);

        // Handle the image upload
        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->image->store('images', 'public');
        }

        $post = Post::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'body' => $request->body,
            'image_path' => $imagePath // assuming your Post model has an 'image_path' field
        ]);

        return $post;
    }
}
