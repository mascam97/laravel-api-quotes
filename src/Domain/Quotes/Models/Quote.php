<?php

namespace Domain\Quotes\Models;

use Database\Factories\DBQuoteFactory;
use Domain\Quotes\QueryBuilders\QuoteQueryBuilder;
use Domain\Quotes\States\QuoteState;
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
 * @property string $title
 * @property string $content
 * @property QuoteState $state
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read string $excerpt
 * @property User $user
 *
 * @method static DBQuoteFactory factory(...$parameters)
 * @method static QuoteQueryBuilder query()
 */
class Quote extends Model
{
    use HasFactory, CanBeRated, HasStates;

    protected $fillable = [
        'title', 'content',
    ];

    protected $casts = [
        'state' => QuoteState::class,
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return DBQuoteFactory::new();
    }

    public function newEloquentBuilder($query): QuoteQueryBuilder
    {
        return new QuoteQueryBuilder($query);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit($this->content, 75);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
