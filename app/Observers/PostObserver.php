<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Str;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class PostObserver
{
    public function created(Post $post): void
    {
        $message = new Message(
            headers: [
                'origin' => 'main-app',
                'event_type' => 'created',
                'correlation_id' => (string)Str::uuid()
            ],
            body: $post->toArray(),
            key: (string) $post->id
        );

        Kafka::publishOn('posts')->withMessage($message)->send();
    }

    public function updated(Post $post): void
    {
    }

    public function deleted(Post $post): void
    {
    }

    public function restored(Post $post): void
    {
    }

    public function forceDeleted(Post $post): void
    {
    }
}
