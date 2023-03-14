<?php

namespace App\Services\Concerns;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KafkaHandler
{
    use KafkaPerform;

    public function __invoke(\Junges\Kafka\Contracts\KafkaConsumerMessage $message)
    {
        $headers = $message->getHeaders();
        $payload = $message->getBody();

        Log::channel('kafka')->info(sprintf('[KAFKA] CONSUME Headers: %s; Payload: %s', json_encode($headers), json_encode($payload)));

        if (!isset($headers['event'])) {
            return;
        }

        $method = 'handle' . Str::studly(str_replace('.', '_', $headers['event']));

        if (!method_exists($this, $method)) {
            return null;
        }

        $this->{$method}($payload);

    }
}
