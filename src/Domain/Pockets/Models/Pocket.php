<?php

namespace Domain\Pockets\Models;

use Database\Factories\DBPocketFactory;
use Domain\Pockets\QueryBuilders\PocketQueryBuilder;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 *
 * @property int $balance
 * @property string $currency
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property-read User $user
 *
 * @method static DBPocketFactory factory(...$parameters)
 * @method static PocketQueryBuilder query()
 */
class Pocket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'balance' => 'int',
    ];

    public function newEloquentBuilder($query): PocketQueryBuilder
    {
        return new PocketQueryBuilder($query);
    }

    /**
     * @return Factory<Pocket>
     */
    protected static function newFactory(): Factory
    {
        return DBPocketFactory::new();  /** @phpstan-return Factory<Pocket> */
    }

    /**
     * @return HasOne<User>
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
