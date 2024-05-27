<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Str;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class UserObserver
{
    public function created(User $user): void
    {
        logger("Sending kafka user created message");

        $message = new Message(
            headers: [
                'origin' => 'main-app',
                'event_type' => 'created',
                'correlation_id' => (string)Str::uuid()
            ],
            body: $user->only(['name', 'email']),
            key: (string) $user->id
        );

        Kafka::publishOn('users')->withMessage($message)->send();

        logger("Apperently sent the message?");
    }

    public function deleted(Comment $comment): void
    {
    }
}
