<?php

declare(strict_types=1);

use Rector\Core\ValueObject\PhpVersion;
use Rector\Laravel\Set\LaravelSetList;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);
    $rectorConfig->sets([
        LaravelSetList::LARAVEL_80,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::PHP_80,
    ]);
    $rectorConfig->phpVersion(PhpVersion::PHP_80);
};
