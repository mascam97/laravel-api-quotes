<?php

namespace Domain\Quotes\States;

class Banned extends QuoteState
{
    public function name(): string
    {
        return 'BANNED';
    }
}
