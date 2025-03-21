<?php

namespace Domain\Users\Models;

use Database\Factories\DBUserFactory;
use Domain\Gifts\Models\Gift;
use Domain\Pockets\Models\Pocket;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Contracts\Rates;
use Domain\Rating\Utils\CanRate;
use Domain\Users\Enums\SexEnum;
use Domain\Users\QueryBuilders\UserQueryBuilder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property-read int $id
 *
 * @property int $pocket_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $locale
 * @property ?SexEnum $sex
 * @property ?Carbon $birthday
 * @property ?Carbon $email_verified_at
 * @property ?Carbon $email_subscribed_at
 * @property ?Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?int $quotes_count
 * @property ?int $permissions_count
 * @property ?int $roles_count
 *
 * @property-read HasMany $quotes
 * @property-read HasMany $gifts
 * @property-read HasMany $sentGifts
 * @property-read Pocket $pocket
 *
 * @method static DBUserFactory factory(...$parameters)
 * @method static UserQueryBuilder query()
 */
class User extends Authenticatable implements Rates, MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable, HasApiTokens, CanRate, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'sex',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_subscribed_at' => 'datetime',
        'sex' => SexEnum::class,
        'quotes_count' => 'int',
        'roles_count' => 'int',
        'permissions_count' => 'int',
    ];

    public function newEloquentBuilder($query): UserQueryBuilder
    {
        return new UserQueryBuilder($query);
    }

    /**
     * @return HasMany<Quote>
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * @return BelongsTo<Pocket, User>
     */
    public function pocket(): BelongsTo
    {
        return $this->belongsTo(Pocket::class);
    }

    /**
     * @return HasMany<Gift>
     */
    public function gifts(): HasMany
    {
        return $this->hasMany(Gift::class);
    }

    /**
     * @return HasMany<Gift>
     */
    public function sentGifts(): HasMany
    {
        return $this->hasMany(Gift::class, 'sender_user_id');
    }

    /**
     * @return Factory<User>
     */
    protected static function newFactory(): Factory
    {
        return DBUserFactory::new();  /** @phpstan-return Factory<User> */
    }
}
