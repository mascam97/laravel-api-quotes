<?php

declare(strict_types=1);

namespace Domain\Rating\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

// TODO: Implements contracts
interface IsRated
{
    public function qualifiers(): MorphToMany;

    public function averageRating(): ?float;
}
