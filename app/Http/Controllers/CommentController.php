<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function show(Comment $comment)
    {
        return $comment;
    }

    public function store(Request $request, Post $post)
    {
        $this->validate($request, [
            'body' => 'required|max:500',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'post_id' => $post->id,
            'body' => $request->body,
        ]);

        return $comment;
    }
}
