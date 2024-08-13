<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainBrand extends Model
{
    use HasFactory;

    protected $table = 'main_brand';

    protected $fillable = [
        'name',
        'follow_tags',
        'mentions',
        'past_stamp',
        'account_id'
    ];

    public function account(): BelongsTo {
        return $this->belongsTo(Account::class);
    }

    public function brands() {
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
}
