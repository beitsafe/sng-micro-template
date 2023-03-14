<?php

namespace App\Services;


use App\Services\Concerns\KafkaHandler;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Config\Sasl;
use Junges\Kafka\Consumers\Consumer;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class KafkaService
{
    private static function saslConfig(): Sasl
    {
        return new Sasl(
            env('KAFKA_SASL_USERNAME'),
            env('KAFKA_SASL_PASSWORD'),
            env('KAFKA_SASL_MECHANISMS'),
            env('KAFKA_SECURITY_PROTOCOL')
        );
    }

    public static function produce(string $topic, $event, $message): bool
    {
        if (!$message instanceof Message) {
            $message = new Message(
                headers: ['event' => $event],
                body: $message
            );
        }

        Log::channel('kafka')->info(sprintf('[KAFKA] PRODUCE Topic: %s; Event: %s; Payload: %s', $topic, $event, json_encode($message->toArray())));

        return Kafka::publishOn($topic)->withSasl(self::saslConfig())->withMessage($message)->send();
    }

    public static function consumer(array $topic): Consumer
    {
        return Kafka::createConsumer($topic)
            ->withSasl(self::saslConfig())
            ->withHandler(new KafkaHandler())
            ->build();
    }
}
