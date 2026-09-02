<?php

declare(strict_types=1);

namespace SimpleBus\Message\Serialization;

use SimpleBus\Message\TypedPayload;

interface Serializer
{
    public function serialize(object $payload): string;

    /**
     * @template T of TypedPayload
     * @param class-string<T> $messageClass
     * @return T
     */
    public function deserialize(string $payload, string $messageClass): object;

    public function getType(): string;
}
