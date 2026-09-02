<?php

declare(strict_types=1);

namespace SimpleBus\Message\Id;

final class IncrementIdGenerator implements IdGenerator
{
    public function __construct(private int $id = 0)
    {
    }

    public function generate(): string
    {
        $this->id++;

        return (string) $this->id;
    }
}
