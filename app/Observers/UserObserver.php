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
            body: [
                "email" => $user->email,
                "name" => $user->first_name . ' ' . $user->last_name,
                "uuid" => $user->uuid,
                "username" => $user->username
            ],
            key: (string) $user->uuid
        );

        Kafka::publishOn('users')->withMessage($message)->send();

        logger("Apperently sent the message?");
    }

    public function deleted(Comment $comment): void
    {
    }
}
