<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, bool>
     */

    protected $fillable = [
        'url',
        'platform_id',
        'title',
        'description',
        'tags',
        'likes',
        'shares',
        'reactions_positive',
        'reactions_negative',
        'reactions_neutral',
        'item_url',
        'is_video',
        'is_image',
        'is_external',
        'mentions',
        'num_comments',
        'view_count',
        'internal_platform_id'
    ];

    public function platform(): BelongsTo {
        return $this->belongsTo(Platform::class);
    }

    public function comment(): HasMany {
        return $this->hasMany(Comment::class);
    }
}
