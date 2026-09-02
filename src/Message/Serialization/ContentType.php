<?php

declare(strict_types=1);

namespace SimpleBus\Message\Serialization;

enum ContentType: string
{
    case Json = 'application/json';
    case Native = 'php/native';
}
