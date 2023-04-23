<?php

namespace Services\Concerns;

use Illuminate\Support\Facades\Http;

trait CanBeFaked
{
    /**
     * Proxy Fake request call through to Http::fake()
     */
    public static function fake(\Closure|array|null $callback = null): void
    {
        Http::fake(
            callback: $callback,
        );
    }
}
