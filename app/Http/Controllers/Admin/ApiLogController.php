<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ApiLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']); // Only allow admin/super-admin
    }

    public function index(Request $request)
    {
        $query = ApiLog::query();

        // Filter by API type
        if ($request->filled('api_type')) {
            $query->byApiType($request->api_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('endpoint', 'like', "%$search%")
                  ->orWhere('error_message', 'like', "%$search%")
                  ->orWhere('created_by', 'like', "%$search%")
                  ->orWhere('ip_address', 'like', "%$search%");
            });
        }

        // Get logs with pagination
        $logs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get filter options
        $apiTypes = ApiLog::getApiTypeOptions();
        $statuses = ApiLog::getStatusOptions();

        // Get statistics
        $stats = [
            'total' => ApiLog::count(),
            'success' => ApiLog::byStatus(ApiLog::STATUS_SUCCESS)->count(),
            'failed' => ApiLog::byStatus(ApiLog::STATUS_FAILED)->count(),
            'error' => ApiLog::byStatus(ApiLog::STATUS_ERROR)->count(),
        ];

        return view('admin.api_logs.index', compact('logs', 'apiTypes', 'statuses', 'stats'));
    }

    public function show($id)
    {
        $log = ApiLog::findOrFail($id);
        return view('admin.api_logs.show', compact('log'));
    }

    public function destroy($id)
    {
        $log = ApiLog::findOrFail($id);
        $log->delete();
        return redirect()->route('admin.api-logs.index')->with('success', 'API log deleted successfully.');
    }

    public function clear(Request $request)
    {
        $query = ApiLog::query();

        // Clear by API type
        if ($request->filled('api_type')) {
            $query->byApiType($request->api_type);
        }

        // Clear by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Clear by date range
        if ($request->filled('days')) {
            $days = $request->days;
            $query->where('created_at', '<=', now()->subDays($days));
        }

        $deletedCount = $query->delete();

        return redirect()->route('admin.api-logs.index')->with('success', "$deletedCount API logs cleared successfully.");
    }

    public function export(Request $request)
    {
        $query = ApiLog::query();

        // Apply same filters as index
        if ($request->filled('api_type')) {
            $query->byApiType($request->api_type);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'api_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'API Type', 'Endpoint', 'Method', 'Status Code', 
                'Status', 'Response Time (ms)', 'Error Message', 'Created By', 
                'IP Address', 'Created At'
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->api_type,
                    $log->endpoint,
                    $log->method,
                    $log->status_code,
                    $log->status,
                    $log->response_time_ms,
                    $log->error_message,
                    $log->created_by,
                    $log->ip_address,
                    $log->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 