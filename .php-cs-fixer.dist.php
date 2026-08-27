<?php

declare(strict_types=1);

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
    ])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setCacheFile(__DIR__ . '/var/cache/php-cs-fixer.cache')
    ->setUsingCache(true);
