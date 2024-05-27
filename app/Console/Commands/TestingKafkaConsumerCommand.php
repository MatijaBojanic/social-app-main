<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;

class TestingKafkaConsumerCommand extends Command
{
    protected $signature = 'testing:kafka-consumer';

    protected $description = 'Command description';

    public function handle(): void
    {
        echo(config('kafka.brokers'));
        echo("\n");

        $consumer = Kafka::createConsumer()->withHandler(function($message) {
            dd($message);
        })->subscribe('topic')->build();

        while (true) {
            $consumer->consume(); // wait for up to 2 minutes for a message
            sleep(1);
        }
    }
}
