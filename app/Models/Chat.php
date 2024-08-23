<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    use HasFactory;

    protected $table = "chat";

    protected $fillable = [
        "name",
        "text",
        'thread_id',
        "main_brand_id"
    ];

    public function mainBrand(): BelongsTo {
        return $this->belongsTo(MainBrand::class);
    }
}
