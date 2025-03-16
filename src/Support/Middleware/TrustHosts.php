<?php

namespace Support\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string>
     */
    public function hosts(): array
    {
        return array_filter([
            $this->allSubdomainsOfApplicationUrl(),
        ], fn ($host) => ! is_null($host));
    }
}
