<?php

namespace App\Observers;

use App\Models\Comment;
use Illuminate\Support\Str;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        logger("Sending kafka comment created message");

        $message = new Message(
            headers: [
                'origin' => 'main-app',
                'event_type' => 'created',
                'correlation_id' => (string)Str::uuid()
            ],
            body: $comment->toArray(),
            key: (string) $comment->id
        );

        Kafka::publishOn('comments')->withMessage($message)->send();

        logger("Apperently sent the message?");
    }

    public function updated(Comment $comment): void
    {
    }

    public function deleted(Comment $comment): void
    {
    }

    public function restored(Comment $comment): void
    {
    }

    public function forceDeleted(Comment $comment): void
    {
    }
}
