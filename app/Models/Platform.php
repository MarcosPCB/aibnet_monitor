<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $table = 'platform';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, bool>
     */

     protected $fillable = [
        'type',
        'url',
        'platform_id',
        'platform_id2',
        'name',
        'avatar_url',
        'description',
        'tags',
        'num_followers',
        'num_likes',
        'capture_comments',
        'capture_users_from_comments',
        'active',
        'brand_id'
    ];

    public function brand(): BelongsTo {
        return $this->belongsTo(Brand::class);
    }

    public function post(): HasMany {
        return $this->hasMany(Post::class);
    }
}
