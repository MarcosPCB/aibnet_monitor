<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, bool>
     */

     protected $fillable = [
        'url',
        'platform_id',
        'message',
        'likes',
        'shares',
        'mentions',
        'reactions_positive',
        'reactions_negative',
        'reactions_neutral',
        'item_url',
        'has_video',
        'has_image',
        'has_external',
        'user_gender', //Male or female only
        'user_age',
        'num_user_followers',
        'post_id'
    ];

    public function post(): BelongsTo {
        return $this->belongsTo(Post::class);
    }
}
