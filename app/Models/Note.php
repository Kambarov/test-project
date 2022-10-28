<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'author_id'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function scopeFilterByAuthorId(Builder $builder, ?int $authorId): Builder
    {
        if (!is_null($authorId)) {
            return $builder->where('author_id', $authorId);
        }

        return $builder;
    }

    public function scopeSearch(Builder $builder, ?string $query): Builder
    {
        if (!is_null($query)) {
            return $builder->where('name', 'like', "%$query%");
        }

        return $builder;
    }
}
