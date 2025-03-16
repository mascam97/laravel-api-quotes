<?php

namespace Services\Concerns;

use Closure;
use Illuminate\Support\Facades\Http;

trait CanBeFaked
{
    /**
     * Proxy Fake request call through to Http::fake()
     *
     * @param array<string, callable>|Closure|null $callback
     */
    public static function fake(Closure|array|null $callback = null): void
    {
        Http::fake(
            callback: $callback,
        );
    }
}
