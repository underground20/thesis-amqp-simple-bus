<?php

declare(strict_types=1);

namespace SimpleBus\Message;

use SimpleBus\Message\Id\IdGenerator;
use SimpleBus\Message\Serialization\Serializer;
use Thesis\Amqp\DeliveryMode;
use Thesis\Amqp\Message;

final readonly class MessageFactory
{
    public function __construct(
        private Serializer $serializer,
        private IdGenerator $messageIdGenerator,
    ) {
    }

    public function create(Envelope $envelope): Message
    {
        $body = $this->serializer->serialize($envelope->payload);

        return new Message(
            body: $body,
            headers: $envelope->headers,
            contentType: $this->serializer->getType(),
            deliveryMode: DeliveryMode::Persistent,
            messageId: $envelope->messageId ?: $this->messageIdGenerator->generate(),
            type: $envelope->payload::type(),
        );
    }
}
