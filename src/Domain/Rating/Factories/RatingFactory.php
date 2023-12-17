<?php

namespace Domain\Rating\Factories;

use Domain\Quotes\Models\Quote;
use Domain\Rating\Exceptions\InvalidScoreException;
use Domain\Users\Models\User;

class RatingFactory
{
    private User $user;

    private Quote $quote;

    public function withUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function withQuote(Quote $quote): static
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @throws InvalidScoreException
     */
    public function create(int $score): void
    {
        $this->user->rate($this->quote, $score);
    }
}
