<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiIntegration;
use Illuminate\Support\Facades\Auth;
use App\Services\ShiprocketService;
use App\Models\ApiType;
use App\Models\ApiLog;

class ApiIntegrationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']); // Only allow admin/super-admin
    }

    public function index(Request $request)
    {
        $query = ApiIntegration::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('type', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('curl_command', 'like', "%$search%")
                  ->orWhere('updated_by', 'like', "%$search%") ;
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $integrations = $query->orderBy('type')->paginate(10)->withQueryString();
        $types = ApiIntegration::select('type')->distinct()->pluck('type');
        return view('admin.api_integrations.index', compact('integrations', 'types'));
    }

    public function create()
    {
        $apiTypes = ApiType::orderBy('name')->get();
        return view('admin.api_integrations.create', compact('apiTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'password' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $value === $request->email) {
                        $fail('Password cannot be the same as email.');
                    }
                },
            ],
            'curl_command' => 'nullable|string|max:255',
            'meta' => 'nullable',
            'login_url' => 'nullable|url',
            'tracking_url' => 'nullable|string',
        ]);
        
        $data = $request->only(['type', 'curl_command']);
        
        // Handle Shiprocket-specific fields
        if ($request->type === 'shiprocket') {
            $data['email'] = $request->email;
            if ($request->filled('password') && $request->password !== $request->email) {
                $data['password'] = $request->password;
            }
            
            // Store Shiprocket-specific URLs in meta
            $meta = [];
            $meta['login_url'] = $request->login_url ?: 'https://apiv2.shiprocket.in/v1/external/auth/login';
            $meta['tracking_url'] = $request->tracking_url ?: 'https://apiv2.shiprocket.in/v1/external/courier/track/awb/{awb}';
            $data['meta'] = $meta;
        } else {
            // Handle generic fields
            $data['email'] = $request->email_generic;
            if ($request->filled('password_generic')) {
                $data['password'] = $request->password_generic;
            }
            
            // Handle meta field for other types
            $meta = $request->meta;
            if ($meta) {
                $decoded = json_decode($meta, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return back()->withInput()->withErrors(['meta' => 'Meta must be valid JSON.']);
                }
                $data['meta'] = $decoded;
            }
        }
        
        $data['updated_by'] = Auth::user()->name ?? 'admin';
        ApiIntegration::create($data);
        return redirect()->route('admin.api-integrations.index')->with('success', 'API Integration added successfully.');
    }

    public function edit($id)
    {
        $integration = ApiIntegration::findOrFail($id);
        $apiTypes = ApiType::orderBy('name')->get();
        return view('admin.api_integrations.edit', compact('integration', 'apiTypes'));
    }

    public function update(Request $request, $id)
    {
        try {
            $integration = ApiIntegration::findOrFail($id);
            
            // Add detailed logging before validation
            \Log::info('API Integration Update - Before Validation', [
                'request_id' => $id,
                'all_inputs' => $request->all(),
                'email' => $request->email,
                'password' => $request->password,
                'filled_password' => $request->filled('password'),
                'password_length' => $request->password ? strlen($request->password) : 0,
            ]);
            
            $request->validate([
                'type' => 'required|string|max:255',
                'email' => 'nullable|string|max:255',
                'password' => [
                    'nullable',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value && $value === $request->email) {
                            $fail('Password cannot be the same as email.');
                        }
                    },
                ],
                'curl_command' => 'nullable|string|max:255',
                'meta' => 'nullable',
                'login_url' => 'nullable|url',
                'tracking_url' => 'nullable|string',
            ]);
            
            \Log::info('API Integration Update - Validation passed');
            
            $data = $request->only(['type', 'curl_command']);
            
            // Handle Shiprocket-specific fields
            if ($request->type === 'shiprocket') {
                $data['email'] = $request->email;
                
                // Only update password if it's filled and different from email
                if ($request->filled('password') && $request->password !== $request->email) {
                    $data['password'] = $request->password;
                    \Log::info('Password will be updated', ['new_password' => $request->password]);
                } else {
                    // Keep existing password if not provided or same as email
                    \Log::info('Password not updated - keeping existing', [
                        'filled_password' => $request->filled('password'),
                        'password_same_as_email' => $request->password === $request->email,
                        'existing_password' => $integration->password
                    ]);
                }
                
                // Store Shiprocket-specific URLs in meta
                $meta = $integration->meta ?? [];
                $meta['login_url'] = $request->login_url ?: 'https://apiv2.shiprocket.in/v1/external/auth/login';
                $meta['tracking_url'] = $request->tracking_url ?: 'https://apiv2.shiprocket.in/v1/external/courier/track/awb/{awb}';
                $data['meta'] = $meta;
            } else {
                // Handle generic fields
                $data['email'] = $request->email_generic;
                if ($request->filled('password_generic')) {
                    $data['password'] = $request->password_generic;
                }
                
                // Handle meta field for other types
                $meta = $request->meta;
                if ($meta) {
                    $decoded = json_decode($meta, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return back()->withInput()->withErrors(['meta' => 'Meta must be valid JSON.']);
                    }
                    $data['meta'] = $decoded;
                }
            }
            
            $data['updated_by'] = Auth::user()->name ?? 'admin';
            
            \Log::info('API Integration Update - Final Data', [
                'data_to_update' => $data,
                'current_integration' => $integration->toArray(),
            ]);
            
            // Check if password field is in the data array
            \Log::info('Password field check', [
                'password_in_data' => isset($data['password']),
                'password_value' => $data['password'] ?? 'NOT_SET',
            ]);
            
            $result = $integration->update($data);
            
            \Log::info('API Integration Update - Update Result', [
                'update_result' => $result,
                'updated_integration' => $integration->fresh()->toArray(),
            ]);
            
            return redirect()->route('admin.api-integrations.index')->with('success', 'API Integration updated successfully.');
            
        } catch (\Exception $e) {
            \Log::error('API Integration Update - Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withInput()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $integration = ApiIntegration::findOrFail($id);
        $integration->delete();
        return redirect()->route('admin.api-integrations.index')->with('success', 'API Integration deleted successfully.');
    }

    public function testConnection(Request $request)
    {
        $type = $request->input('type');
        
        // Debug logging
        \Log::info('Test connection request', [
            'type' => $type,
            'all_inputs' => $request->all()
        ]);
        
        if (empty($type)) {
            return response()->json(['success' => false, 'message' => 'API type is required. Please select an API type from the dropdown.']);
        }
        
        switch ($type) {
            case 'shiprocket':
                return $this->testShiprocketConnection($request);
                
            case 'delhivery':
                return $this->testDelhiveryConnection($request);
                
            case 'other':
                return $this->testGenericConnection($request);
                
            default:
                return response()->json(['success' => false, 'message' => 'Unknown API type: "' . $type . '". Available types: shiprocket, delhivery, other']);
        }
    }
    
    private function testShiprocketConnection(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        // If password is blank, fetch from DB using integration id
        if (empty($password)) {
            // Try to get integration id from request (for edit page)
            $integrationId = $request->input('id') ?? $request->route('id');
            if ($integrationId) {
                $integration = \App\Models\ApiIntegration::find($integrationId);
                if ($integration) {
                    $password = $integration->password;
                }
            }
        }
        \Log::info('Shiprocket Test Params', [
            'email' => $email,
            'password' => $password,
            'all_inputs' => $request->all()
        ]);
        $loginUrl = $request->input('login_url') ?: 'https://apiv2.shiprocket.in/v1/external/auth/login';
        
        if (empty($email) || empty($password)) {
            return response()->json(['success' => false, 'message' => 'Email and password are required for Shiprocket authentication.']);
        }
        
        // Create API log entry
        $apiLog = ApiLog::create([
            'api_type' => 'shiprocket',
            'endpoint' => $loginUrl,
            'method' => 'POST',
            'request_data' => ['email' => $email, 'password' => $password],
            'status' => ApiLog::STATUS_PENDING,
            'created_by' => Auth::user()->name ?? 'admin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        $startTime = microtime(true);
        
        try {
            $response = \Illuminate\Support\Facades\Http::post($loginUrl, [
                'email' => $email,
                'password' => $password,
            ]);
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000); // Convert to milliseconds
            
            if ($response->successful() && isset($response['token'])) {
                // Update log with success
                $apiLog->update([
                    'response_data' => ['token' => substr($response['token'], 0, 20) . '...'],
                    'status_code' => $response->status(),
                    'status' => ApiLog::STATUS_SUCCESS,
                    'response_time_ms' => $responseTime,
                ]);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Shiprocket authentication successful! Token received: ' . substr($response['token'], 0, 20) . '...'
                ]);
            } else {
                $errorMessage = $response['message'] ?? 'Authentication failed';
                
                // Update log with failure
                $apiLog->update([
                    'response_data' => $response->json(),
                    'status_code' => $response->status(),
                    'status' => ApiLog::STATUS_FAILED,
                    'error_message' => $errorMessage,
                    'response_time_ms' => $responseTime,
                ]);
                
                return response()->json(['success' => false, 'message' => 'Shiprocket authentication failed: ' . $errorMessage]);
            }
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            
            // Update log with error
            $apiLog->update([
                'status' => ApiLog::STATUS_ERROR,
                'error_message' => $e->getMessage(),
                'response_time_ms' => $responseTime,
            ]);
            
            return response()->json(['success' => false, 'message' => 'Connection error: ' . $e->getMessage()]);
        }
    }
    
    private function testDelhiveryConnection(Request $request)
    {
        $email = $request->input('email_generic') ?: $request->input('email');
        $password = $request->input('password_generic') ?: $request->input('password');
        $curlCommand = $request->input('curl_command');
        
        if (empty($curlCommand)) {
            return response()->json(['success' => false, 'message' => 'API endpoint URL is required for Delhivery connection test.']);
        }
        
        // Create API log entry
        $apiLog = ApiLog::create([
            'api_type' => 'delhivery',
            'endpoint' => $curlCommand,
            'method' => 'GET',
            'request_data' => ['endpoint' => $curlCommand],
            'status' => ApiLog::STATUS_PENDING,
            'created_by' => Auth::user()->name ?? 'admin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        $startTime = microtime(true);
        
        try {
            // Test basic connectivity to the API endpoint
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($curlCommand);
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            
            if ($response->successful()) {
                // Update log with success
                $apiLog->update([
                    'response_data' => $response->json(),
                    'status_code' => $response->status(),
                    'status' => ApiLog::STATUS_SUCCESS,
                    'response_time_ms' => $responseTime,
                ]);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Delhivery API endpoint is accessible. Status: ' . $response->status()
                ]);
            } else {
                // Update log with failure
                $apiLog->update([
                    'response_data' => $response->json(),
                    'status_code' => $response->status(),
                    'status' => ApiLog::STATUS_FAILED,
                    'error_message' => 'HTTP Status: ' . $response->status(),
                    'response_time_ms' => $responseTime,
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Delhivery API endpoint returned status: ' . $response->status()
                ]);
            }
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            
            // Update log with error
            $apiLog->update([
                'status' => ApiLog::STATUS_ERROR,
                'error_message' => $e->getMessage(),
                'response_time_ms' => $responseTime,
            ]);
            
            return response()->json(['success' => false, 'message' => 'Connection error: ' . $e->getMessage()]);
        }
    }
    
    private function testGenericConnection(Request $request)
    {
        $email = $request->input('email_generic') ?: $request->input('email');
        $password = $request->input('password_generic') ?: $request->input('password');
        $curlCommand = $request->input('curl_command');
        
        if (empty($curlCommand)) {
            return response()->json(['success' => false, 'message' => 'API endpoint URL is required for connection test.']);
        }
        
        // Create API log entry
        $apiLog = ApiLog::create([
            'api_type' => 'other',
            'endpoint' => $curlCommand,
            'method' => 'GET',
            'request_data' => ['endpoint' => $curlCommand],
            'status' => ApiLog::STATUS_PENDING,
            'created_by' => Auth::user()->name ?? 'admin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        $startTime = microtime(true);
        
        try {
            // Test basic connectivity to the API endpoint
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($curlCommand);
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            
            if ($response->successful()) {
                // Update log with success
                $apiLog->update([
                    'response_data' => $response->json(),
                    'status_code' => $response->status(),
                    'status' => ApiLog::STATUS_SUCCESS,
                    'response_time_ms' => $responseTime,
                ]);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'API endpoint is accessible. Status: ' . $response->status()
                ]);
            } else {
                // Update log with failure
                $apiLog->update([
                    'response_data' => $response->json(),
                    'status_code' => $response->status(),
                    'status' => ApiLog::STATUS_FAILED,
                    'error_message' => 'HTTP Status: ' . $response->status(),
                    'response_time_ms' => $responseTime,
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'API endpoint returned status: ' . $response->status()
                ]);
            }
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            
            // Update log with error
            $apiLog->update([
                'status' => ApiLog::STATUS_ERROR,
                'error_message' => $e->getMessage(),
                'response_time_ms' => $responseTime,
            ]);
            
            return response()->json(['success' => false, 'message' => 'Connection error: ' . $e->getMessage()]);
        }
    }
} 