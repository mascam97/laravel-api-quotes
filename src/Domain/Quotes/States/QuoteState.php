<?php

namespace Domain\Quotes\States;

use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class QuoteState extends State
{
    abstract public function name(): string;

    /**
     * @throws InvalidConfig
     */
    public static function config(): StateConfig
    {
//        TODO: Implement better logic in index quotes, authorization, how a quote becomes banned
        return parent::config()
            ->default(Drafted::class)
            ->allowTransition(Drafted::class, Published::class)
            ->allowTransition(Published::class, Banned::class);
    }
}
