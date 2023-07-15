<?php

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index method returns posts from the followed users and the user itself', function () {
    // Arrange
    $user = User::factory()->create();
    $followedUsers = User::factory()->count(3)->create();
    $user->following()->sync($followedUsers->pluck('id'));

    // Act
    foreach($followedUsers as $followedUser) {
        Post::factory()->create([
            'user_id' => $followedUser->id,
        ]);
    }

    // User posts
    $userPosts = Post::factory()->count(2)->create([
        'user_id' => $user->id,
    ]);

    auth()->login($user);

    $response = $this->get('/api/posts');

    // Assert
    $response->assertStatus(200);
    foreach($followedUsers as $followedUser) {
        $response->assertSee($followedUser->posts->first()->content);
    }

    foreach($userPosts as $userPost) {
        $response->assertSee($userPost->content);
    }
});

test('returns the correct post and comments', function () {
    $user = User::factory()->create();
    auth()->login($user);
    // Assuming you have a Post factory and Comment factory
    $post = Post::factory()->create(['user_id' => $user->id]);

    $comments = Comment::factory()->count(3)->create(['post_id' => $post->id]);

    $response = $this->getJson('/api/posts/'. $post->id); // replace with your route

    $response->assertOk();
    $response->assertJsonPath('id', $post->id);

    foreach ($comments as $comment) {
        $response->assertJsonFragment([
            'id' => $comment->id,
            'body' => $comment->body,
            // add more comment fields here
        ]);
    }
});
