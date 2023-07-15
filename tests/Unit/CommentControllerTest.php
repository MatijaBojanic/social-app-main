<?php

use App\Http\Controllers\PostController;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Junges\Kafka\Facades\Kafka;

uses(RefreshDatabase::class);

test('returns the correct post and comments', function () {
    $user = User::factory()->create();
    auth()->login($user);
    // Assuming you have a Post factory and Comment factory
    $post = Post::factory()->create(['user_id' => $user->id]);

    $comment = Comment::factory()->create(['post_id' => $post->id]);

    $response = $this->getJson('/api/comments/'. $comment->id); // replace with your route

    $response->assertOk();
    $response->assertJsonPath('id', $comment->id);

    $response->assertJsonFragment([
        'id' => $comment->id,
        'body' => $comment->body,
    ]);
});

test('stores a comment', function() {
    // Create and login a user
    $user = User::factory()->create();
    Auth::login($user);

    $post = Post::factory()->create(['user_id' => $user->id]);

    $postData = [
        'body' => 'This is a test comment',
    ];

    // Make a POST request
    $response = $this->postJson('/api/posts/' . $post->id . '/comments', $postData);

    $response->assertStatus(201);

    // Check that the database has the post
    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'body' => 'This is a test comment'
    ]);

    // Check if response has the post data
    $response->assertJsonStructure(['id', 'user_id', 'body']);
    $response->assertJsonPath('body', $postData['body']);
});

test('Post created sent to kafka', function() {
    Kafka::fake();

    $user = User::factory()->create();
    // This should trigger the PostObserver@created method, which should send the Kafka message
    $post = Post::factory()->create(['user_id' => $user->id]);
    $comment = Comment::factory()->create(['post_id' => $post->id]);
    // Assert that the expected Kafka message was published when the Post was created
    Kafka::assertPublishedOn('comments');
});
