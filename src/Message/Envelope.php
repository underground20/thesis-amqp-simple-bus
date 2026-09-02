<?php

declare(strict_types=1);

namespace SimpleBus\Message;

final class Envelope
{
    /** @param array<string, mixed> $headers */
    public function __construct(
        public TypedPayload $payload,
        public string $messageId = '',
        public array $headers = [],
    ) {
    }
}
