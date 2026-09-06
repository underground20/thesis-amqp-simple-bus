<?php

declare(strict_types=1);

namespace SimpleBus\Producer;

final readonly class PublishOptions
{
    public function __construct(
        public string $exchange = '',
        public string $routingKey = '',
    ) {
    }
}
