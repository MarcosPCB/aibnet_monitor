<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $table = "lead";

    protected $fillable = [
        "name",
        "platform_ids",
        'shortcodes',
        'status',
        'score',
        'email',
        'phone',
        "main_brand_id"
    ];

    public function mainBrand(): BelongsTo {
        return $this->belongsTo(MainBrand::class);
    }
}
