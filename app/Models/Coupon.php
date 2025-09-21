<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    /**
     * Use tenant connection when available, so coupons are client-specific.
     */
    public function newQuery()
    {
        if (!$this->getConnectionName()) {
            if (app()->bound('tenant.connection')) {
                $this->setConnection(app('tenant.connection'));
            } elseif (config('database.default') !== 'main') {
                $this->setConnection(config('database.default'));
            }
        }
        return parent::newQuery();
    }
}
