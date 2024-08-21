<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainBrand extends Model
{
    use HasFactory;

    protected $table = 'main_brand';

    protected $fillable = [
        'name',
        'follow_tags',
        'mentions',
        'past_stamp',
        'chat_model',
        'account_id'
    ];

    public function account(): BelongsTo {
        return $this->belongsTo(Account::class);
    }

    public function brands(): BelongsToMany {
        return $this->belongsToMany(Brand::class, 'main_brand_brand')
                    ->withPivot('is_opponent')
                    ->withTimestamps();
    }

    public function opponents() {
        return $this->brands()->wherePivot('is_opponent', true);
    }

    public function primaryBrand() {
        return $this->brands()->wherePivot('is_opponent', false);
    }

    public function chats(): HasMany {
        return $this->hasMany(Chat::class);
    }
}
