<?php

namespace Domain\Gifts\Models;

use Database\Factories\DBGiftFactory;
use Domain\Quotes\QueryBuilders\QuoteQueryBuilder;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 *
 * @property ?string $note
 * @property int $amount
 * @property string $currency
 * @property int $user_id
 * @property int $sender_user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property User $user
 * @property User $senderUser
 *
 * @method static DBGiftFactory factory(...$parameters)
 * @method static QuoteQueryBuilder query()
 */
class Gift extends Model
{
    use HasFactory;

    public function newEloquentBuilder($query): QuoteQueryBuilder
    {
        return new QuoteQueryBuilder($query);
    }

    /**
     * @return BelongsTo<User, Gift>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, Gift>
     */
    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * @return Factory<Gift>
     */
    protected static function newFactory(): Factory
    {
        return DBGiftFactory::new(); /** @phpstan-return Factory<Gift> */
    }
}
