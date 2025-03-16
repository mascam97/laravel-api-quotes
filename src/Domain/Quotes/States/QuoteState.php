<?php

namespace Domain\Quotes\States;

use Domain\Quotes\Models\Quote;
use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * @extends State<Quote>
 */
abstract class QuoteState extends State
{
    public static string $name;

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
