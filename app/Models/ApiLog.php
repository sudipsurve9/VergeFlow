<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_type',
        'endpoint',
        'method',
        'request_data',
        'response_data',
        'status_code',
        'status',
        'error_message',
        'response_time_ms',
        'user_agent',
        'ip_address',
        'created_by',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'response_time_ms' => 'integer',
        'status_code' => 'integer',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_ERROR = 'error';

    // API type constants
    const TYPE_SHIPROCKET = 'shiprocket';
    const TYPE_DELHIVERY = 'delhivery';
    const TYPE_OTHER = 'other';

    /**
     * Get the API type options
     */
    public static function getApiTypeOptions()
    {
        return [
            self::TYPE_SHIPROCKET => 'Shiprocket',
            self::TYPE_DELHIVERY => 'Delhivery',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get the status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_ERROR => 'Error',
        ];
    }

    /**
     * Scope to filter by API type
     */
    public function scopeByApiType($query, $apiType)
    {
        return $query->where('api_type', $apiType);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted response time
     */
    public function getFormattedResponseTimeAttribute()
    {
        if (!$this->response_time_ms) {
            return 'N/A';
        }
        
        if ($this->response_time_ms < 1000) {
            return $this->response_time_ms . 'ms';
        }
        
        return round($this->response_time_ms / 1000, 2) . 's';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_SUCCESS:
                return 'badge bg-success';
            case self::STATUS_FAILED:
                return 'badge bg-warning';
            case self::STATUS_ERROR:
                return 'badge bg-danger';
            default:
                return 'badge bg-secondary';
        }
    }
}
