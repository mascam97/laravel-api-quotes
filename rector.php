<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    )->withSets([LevelSetList::UP_TO_PHP_84]);
