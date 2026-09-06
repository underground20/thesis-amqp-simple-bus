<?php

declare(strict_types=1);

namespace SimpleBus\Producer;

use SimpleBus\ClientFactory;
use SimpleBus\Message\Envelope;
use SimpleBus\Message\MessageFactory;
use Thesis\Amqp\PublishConfirmation;
use Thesis\Amqp\PublishMessage;

final readonly class ConfirmableProducer implements Producer
{
    public function __construct(
        private ClientFactory $clientFactory,
        private MessageFactory $messageFactory,
    ) {
    }

    public function publish(Envelope $envelope, PublishOptions $publishOptions): void
    {
        $client = $this->clientFactory->create();
        $channel = $client->channel();

        try {
            $channel->confirmSelect();
            $message = $this->messageFactory->create($envelope);
            /** @var PublishConfirmation $confirmation */
            $confirmation = $channel->publish($message, exchange: $publishOptions->exchange, routingKey: $publishOptions->routingKey);
            $result = $confirmation->await();
            $result->ensurePublished();
        } finally {
            $client->disconnect();
        }
    }

    public function publishBatch(array $envelopes, PublishOptions $publishOptions): void
    {
        $client = $this->clientFactory->create();
        $channel = $client->channel();

        $messages = [];
        foreach ($envelopes as $envelope) {
            $messages[] = new PublishMessage(
                message: $this->messageFactory->create($envelope),
                exchange: $publishOptions->exchange,
                routingKey: $publishOptions->routingKey,
            );
        }

        try {
            $channel->confirmSelect();
            $confirmation = $channel->publishBatch($messages);
            $result = $confirmation->await();
            $result->ensureAllPublished();
        } finally {
            $client->disconnect();
        }
    }
}
