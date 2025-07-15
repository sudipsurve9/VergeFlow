<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiType extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
    ];
} 