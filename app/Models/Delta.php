<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delta extends Model
{
    use HasFactory;

    protected $table = 'delta';

    protected $fillable = [
        'week',
        'year',
        'brand_id',
        'json'
    ];

    protected $casts = [
        'json' => 'json'
    ];

    public function brand(): BelongsTo {
        return $this->belongsTo(Brand::class);
    }
}
