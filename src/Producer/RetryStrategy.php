<?php

declare(strict_types=1);

namespace SimpleBus\Producer;

final readonly class RetryStrategy
{
    public function __construct(
        private int $maxAttempts = 3,
        private float $delay = 0.5,
        private float $maxDelay = 0.5,
    ) {
    }

    /**
     * @param \Closure(): void $publish
     */
    public function execute(\Closure $publish): void
    {
        $lastException = null;
        for ($attempt = 1; $attempt <= $this->maxAttempts; $attempt++) {
            try {
                $publish();
                return;
            } catch (\Throwable $e) {
                $lastException = $e;
                if ($attempt === $this->maxAttempts) {
                    break;
                }

                usleep((int)($this->calculateDelay($attempt) * 1_000_000));
            }
        }

        if ($lastException === null) {
            return;
        }

        throw $lastException;
    }

    private function calculateDelay(int $attempt): float
    {
        $delay = $this->delay * (2 ** ($attempt - 1));

        return min($delay, $this->maxDelay);
    }
}
