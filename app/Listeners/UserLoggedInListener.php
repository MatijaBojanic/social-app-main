<?php

namespace App\Listeners;

use App\Events\UserLoggedInEvent;
use Illuminate\Support\Str;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class UserLoggedInListener
{
    public function __construct()
    {
    }

    public function handle(UserLoggedInEvent $event): void
    {
        $message = new Message(
            headers: [
                'origin' => 'main-app',
                'event_type' => 'logged-in',
                'correlation_id' => (string)Str::uuid()
            ],
            body: $event->user->toArray(),
            key: (string) $event->user->id
        );

        Kafka::publishOn('users')->withMessage($message)->send();
    }
}
