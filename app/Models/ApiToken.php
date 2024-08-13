<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasFactory;

    protected $table = 'api_tokens';

    protected $fillable = [
        'name',
        'url',
        'doc_url',
        'token',
        'email',
        'limit',
        'limit_type',
        'last_used',
        'limit_used',
        'status',
        'expires'
    ];
}
