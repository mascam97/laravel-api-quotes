<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'content'
    ];

    public function getExcerptAttribute()
    {
        return Str::limit($this->content, 75);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
