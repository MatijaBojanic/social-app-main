<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class TestingKafkaCommand extends Command
{
    protected $signature = 'testing:kafka';

    protected $description = 'Command description';

    public function handle(): void
    {
        echo(config('kafka.brokers'));
        echo("\n");

        $message = new Message(
            headers: ['header-key' => 'header-value'],
            body: ['key' => 'value'],
            key: 'kafka key here'
        );

        Kafka::publishOn('topic')->withMessage($message)->send();
    }
}
