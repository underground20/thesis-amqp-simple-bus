<?php

declare(strict_types=1);

namespace SimpleBus\Producer;

use SimpleBus\Message\Envelope;

final readonly class RetryingProducer implements Producer
{
    public function __construct(
        private Producer $inner,
        private RetryStrategy $strategy,
    ) {
    }

    public function publish(Envelope $envelope, PublishOptions $publishOptions): void
    {
        $this->strategy->execute(fn () => $this->inner->publish($envelope, $publishOptions));
    }

    public function publishBatch(array $envelopes, PublishOptions $publishOptions): void
    {
        $this->strategy->execute(fn () => $this->inner->publishBatch($envelopes, $publishOptions));
    }
}
