<?php

declare(strict_types=1);

namespace Domain\Quotes\States;

class Banned extends QuoteState
{
    public static string $name = 'BANNED';
}
