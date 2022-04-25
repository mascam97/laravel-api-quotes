<?php

namespace Domain\Quotes\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class QuoteQueryBuilder extends Builder
{
    public function whereId(int $id): self
    {
        return $this->where('id', $id);
    }
}
