<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Client;

class HealthController extends Controller
{
    /**
     * Basic health check endpoint for load balancers
     */
    public function check()
    {
        try {
            // Quick database check
            DB::connection('main')->select('SELECT 1');
            
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toIso8601String(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => 'Database connection failed',
                'timestamp' => now()->toIso8601String(),
            ], 503);
        }
    }

    /**
     * Detailed health check endpoint
     */
    public function detailed()
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'checks' => [],
        ];

        // Database check
        try {
            DB::connection('main')->select('SELECT 1');
            $health['checks']['main_database'] = [
                'status' => 'ok',
                'response_time_ms' => $this->measureTime(function() {
                    DB::connection('main')->select('SELECT 1');
                }),
            ];
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['main_database'] = [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }

        // Redis check
        try {
            if (config('cache.default') === 'redis' || config('session.driver') === 'redis') {
                Redis::ping();
                $health['checks']['redis'] = [
                    'status' => 'ok',
                    'response_time_ms' => $this->measureTime(function() {
                        Redis::ping();
                    }),
                ];
            } else {
                $health['checks']['redis'] = [
                    'status' => 'skipped',
                    'reason' => 'Redis not configured',
                ];
            }
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['checks']['redis'] = [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }

        // Cache check
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 10);
            $value = Cache::get($testKey);
            Cache::forget($testKey);
            
            $health['checks']['cache'] = [
                'status' => $value === 'test' ? 'ok' : 'failed',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['checks']['cache'] = [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }

        // Client databases check
        try {
            $clients = Client::count();
            $health['checks']['clients'] = [
                'status' => 'ok',
                'total_clients' => $clients,
            ];
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['checks']['clients'] = [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }

        // Disk space check
        $diskFree = disk_free_space(storage_path());
        $diskTotal = disk_total_space(storage_path());
        $diskUsedPercent = (($diskTotal - $diskFree) / $diskTotal) * 100;
        
        $health['checks']['disk'] = [
            'status' => $diskUsedPercent < 90 ? 'ok' : 'warning',
            'free_gb' => round($diskFree / 1024 / 1024 / 1024, 2),
            'total_gb' => round($diskTotal / 1024 / 1024 / 1024, 2),
            'used_percent' => round($diskUsedPercent, 2),
        ];

        if ($diskUsedPercent >= 90) {
            $health['status'] = 'degraded';
        }

        $statusCode = $health['status'] === 'healthy' ? 200 : ($health['status'] === 'degraded' ? 200 : 503);
        
        return response()->json($health, $statusCode);
    }

    /**
     * Measure execution time of a callable
     */
    private function measureTime(callable $callback): float
    {
        $start = microtime(true);
        $callback();
        $end = microtime(true);
        return round(($end - $start) * 1000, 2);
    }
}

