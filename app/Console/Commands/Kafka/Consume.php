<?php

namespace App\Console\Commands\Kafka;

use App\Services\KafkaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Consume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume Worker For Kafka';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('kafka')->info(sprintf('[KAFKA] CONSUMING Topic(s): %s;', env('KAFKA_CONSUMER_TOPIC', 'default')));

        $consumer = KafkaService::consumer(explode(',', env('KAFKA_CONSUMER_TOPIC', 'default')));

        $consumer->consume();
    }
}
