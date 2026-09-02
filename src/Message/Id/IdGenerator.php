<?php

declare(strict_types=1);

namespace SimpleBus\Message\Id;

interface IdGenerator
{
    public function generate(): string;
}
