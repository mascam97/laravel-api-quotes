<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    )->withSets([SetList::PHP_82]);
