<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'account';

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, bool>
     */

     protected $fillable = [
        'name',
        'token',
        'payment_method',
        'installments',
        'contract_time',
        'paid',
        'contract_type',
        'contract_description',
        'contract_brands',
        'contract_brand_opponents',
        'contract_users',
        'contract_build_brand_time',
        'contract_monitored',
        'cancel_time',
        'active'
    ];

    public function user(): HasMany {
        return $this->hasMany(User::class);
    }

    public function mainBrand(): HasMany {
        return $this->hasMany(MainBrand::class);
    }
}
