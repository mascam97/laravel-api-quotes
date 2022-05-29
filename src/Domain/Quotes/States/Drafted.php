<?php

namespace Domain\Quotes\States;

class Drafted extends QuoteState
{
    public function name(): string
    {
        return 'DRAFTED';
    }
}
