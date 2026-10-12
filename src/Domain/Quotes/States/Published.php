<?php

declare(strict_types=1);

namespace Domain\Quotes\States;

class Published extends QuoteState
{
    public static string $name = 'PUBLISHED';
}
