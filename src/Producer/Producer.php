<?php

declare(strict_types=1);

namespace SimpleBus\Producer;

use SimpleBus\Message\Envelope;

interface Producer
{
    public function publish(Envelope $envelope, PublishOptions $publishOptions): void;

    /** @param non-empty-list<Envelope> $envelopes */
    public function publishBatch(array $envelopes, PublishOptions $publishOptions): void;
}
