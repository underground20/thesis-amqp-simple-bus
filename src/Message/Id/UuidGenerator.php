<?php

declare(strict_types=1);

namespace SimpleBus\Message\Id;

use Ramsey\Uuid\Uuid;

final class UuidGenerator implements IdGenerator
{
    public function generate(): string
    {
        return Uuid::uuid7()->toString();
    }
}
