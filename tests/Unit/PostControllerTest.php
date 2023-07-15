<?php

use App\Http\Controllers\PostController;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Junges\Kafka\Facades\Kafka;

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

test('stores a post', function() {
    // Create and login a user
    $user = User::factory()->create();
    Auth::login($user);

    $postData = [
        'title' => 'This is a test TITLE',
        'body' => 'This is a test post'
    ];

    // Make a POST request
    $response = $this->postJson('/api/posts', $postData);

    $response->assertStatus(201);

    // Check that the database has the post
    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'title' => 'This is a test TITLE',
        'body' => 'This is a test post'
    ]);

    // Check if response has the post data
    $response->assertJsonStructure(['id', 'user_id', 'title', 'body']);
    $response->assertJsonPath('title', $postData['title']);
    $response->assertJsonPath('body', $postData['body']);
});

test('fails to store a post due to validation', function() {
// Create and login a user
    $user = User::factory()->create();
    Auth::login($user);

    // Title required check
    $response = $this->post('/api/posts', [
        'body' => str_repeat('a', 500)
    ]);
    $response->assertSessionHasErrors(['title']);

    // Body required check
    $response = $this->post('/api/posts', [
        'title' => str_repeat('a', 100)
    ]);
    $response->assertSessionHasErrors(['body']);

    // Title And Body max lengths check
    $response = $this->post('/api/posts', [
        'title' => str_repeat('a', 101),
        'body' => str_repeat('a', 501)
    ]);
    $response->assertSessionHasErrors(['body', 'title']);
});

test('Post created sent to kafka', function() {
    Kafka::fake();

    $user = User::factory()->create();
    // This should trigger the PostObserver@created method, which should send the Kafka message
    $post = Post::factory()->create(['user_id' => $user->id]);

    // Assert that the expected Kafka message was published when the Post was created
    Kafka::assertPublishedOn('posts');
});
