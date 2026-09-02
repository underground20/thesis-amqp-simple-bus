<?php

declare(strict_types=1);

namespace SimpleBus\Message;

interface TypedPayload
{
    /** @return non-empty-string */
    public static function type(): string;
}
