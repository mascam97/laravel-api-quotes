<?php

namespace Domain\Users\Models;

use Database\Factories\DBUserFactory;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Utils\CanRate;
use Domain\Users\Enums\SexEnum;
use Domain\Users\Factories\UserFactory;
use Domain\Users\QueryBuilders\UserQueryBuilder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $quotes_count
 * @property-read HasMany $quotes
 *
 * @method static UserFactory factory(...$parameters)
 * @method static UserQueryBuilder query()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, CanRate;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'sex' => SexEnum::class.':nullable',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return DBUserFactory::new();
    }

    public function newEloquentBuilder($query): UserQueryBuilder
    {
        return new UserQueryBuilder($query);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
