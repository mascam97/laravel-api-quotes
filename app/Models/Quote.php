<?php

namespace App\Models;

use App\Utils\CanBeRated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property-read int $id
 * @property string $title
 * @property string $content
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read string $excerpt
 * @property User $user
 */
class Quote extends Model
{
    use HasFactory, CanBeRated;

    protected $fillable = [
        'title', 'content',
    ];

    public function getExcerptAttribute(): string
    {
        return Str::limit($this->content, 75);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
