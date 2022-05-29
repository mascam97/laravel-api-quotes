<?php

namespace Domain\Quotes\States;

class Published extends QuoteState
{
    public function name(): string
    {
        return 'PUBLISHED';
    }
}
