<?php

declare(strict_types=1);

namespace SimpleBus\Message\Serialization;

final class PhpNativeSerializer implements Serializer
{
    public function serialize(object $payload): string
    {
        return serialize($payload);
    }

    public function deserialize(string $payload, string $messageClass): object
    {
        $data = unserialize($payload, ['allowed_classes' => true]);
        if ($data === false || !$data instanceof $messageClass) {
            throw new \InvalidArgumentException("Could not deserialize message $messageClass with payload: $payload");
        }

        return $data;
    }

    public function getType(): string
    {
        return ContentType::Native->value;
    }
}
