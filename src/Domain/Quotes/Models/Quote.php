<?php

namespace Domain\Quotes\Models;

use Database\Factories\DBQuoteFactory;
use Domain\Quotes\QueryBuilders\QuoteQueryBuilder;
use Domain\Quotes\States\QuoteState;
use Domain\Rating\Contracts\IsRated;
use Domain\Rating\Utils\CanBeRated;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\ModelStates\HasStates;

/**
 * @property-read int $id
 *
 * @property ?float $average_score
 * @property string $title
 * @property string $content
 * @property QuoteState $state
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read string $excerpt
 *
 * @property User $user
 *
 * @method static DBQuoteFactory factory(...$parameters)
 * @method static QuoteQueryBuilder query()
 */
class Quote extends Model implements IsRated
{
    use HasFactory, CanBeRated, HasStates;

    protected $fillable = [
        'title', 'content',
    ];

    protected $casts = [
        'state' => QuoteState::class,
    ];

    public function newEloquentBuilder($query): QuoteQueryBuilder
    {
        return new QuoteQueryBuilder($query);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit($this->content, 75);
    }

    public function getAverageUserScore(): ?float
    {
        return $this->averageRating(User::class);
    }

    /**
     * @return BelongsTo<User, Quote>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Factory<Quote>
     */
    protected static function newFactory(): Factory
    {
        /** @var Factory<Quote> */
        return DBQuoteFactory::new();
    }
}
