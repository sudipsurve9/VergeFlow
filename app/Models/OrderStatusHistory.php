<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MultiTenant;

class OrderStatusHistory extends Model
{
    use HasFactory, MultiTenant;

    protected $fillable = [
        'order_id',
        'status',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
} 