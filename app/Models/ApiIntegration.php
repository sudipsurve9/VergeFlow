<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    protected $fillable = [
        'type',
        'email',
        'password',
        'curl_command',
        'meta',
        'updated_by',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
} 