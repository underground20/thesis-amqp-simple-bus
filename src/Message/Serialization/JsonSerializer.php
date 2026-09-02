<?php

declare(strict_types=1);

namespace SimpleBus\Message\Serialization;

final class JsonSerializer implements Serializer
{
    /** @throws \JsonException */
    public function serialize(object $payload): string
    {
        return json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function deserialize(string $payload, string $messageClass): object
    {
        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

        return SimpleHydrator::hydrate($data, $messageClass);
    }

    public function getType(): string
    {
        return ContentType::Json->value;
    }
}
