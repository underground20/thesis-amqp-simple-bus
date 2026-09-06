<?php

declare(strict_types=1);

namespace SimpleBus;

use Thesis\Amqp\Client;
use Thesis\Amqp\Config;

final readonly class ClientFactory
{
    /** @param non-empty-string $dsn */
    public function __construct(private string $dsn)
    {
    }

    public function create(): Client
    {
        $config = Config::fromURI($this->dsn);
        $client = new Client($config);
        $client->connect();

        return $client;
    }
}
