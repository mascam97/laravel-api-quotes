<?php

namespace Domain\Rating\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Rates
{
    /**
     * @return MorphToMany<Model>
     */
    public function ratings(): MorphToMany;

//    TODO: This does not work
//    public function rate();
//
//    public function unrate(): bool;
//
//    public function hasRated(): bool;
}
