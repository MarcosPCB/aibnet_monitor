<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brand';

    protected $fillable = [
        'name',
        'active'
    ];

    public function mainBrand() {
        return $this->belongsToMany(MainBrand::class, 'main_brand_brand')
                    ->withPivot('is_opponent')
                    ->withTimestamps();
    }
}

