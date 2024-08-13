<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delta extends Model
{
    use HasFactory;

    protected $table = 'delta';

    protected $fillable = [
        'week',
        'year',
        'main_brand_id',
        'primary_posts',
        'opponents_posts'
    ];

    protected $casts = [
        'primary_posts' => 'array',
        'opponents_posts' => 'array',
    ];

    public function mainBrand(): BelongsTo {
        return $this->belongsTo(MainBrand::class, 'main_brand');
    }
}
