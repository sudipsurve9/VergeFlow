@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">API Log Details</h1>
        <a href="{{ route('admin.api-logs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>{{ $log->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>API Type:</strong></td>
                            <td><span class="badge bg-info">{{ ucfirst($log->api_type) }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Endpoint:</strong></td>
                            <td><code>{{ $log->endpoint }}</code></td>
                        </tr>
                        <tr>
                            <td><strong>Method:</strong></td>
                            <td><span class="badge bg-secondary">{{ $log->method }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="{{ $log->status_badge_class }}">{{ ucfirst($log->status) }}</span>
                                @if($log->status_code)
                                    <small class="text-muted">({{ $log->status_code }})</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Response Time:</strong></td>
                            <td>{{ $log->formatted_response_time }}</td>
                        </tr>
                        <tr>
                            <td><strong>Created By:</strong></td>
                            <td>{{ $log->created_by }}</td>
                        </tr>
                        <tr>
                            <td><strong>IP Address:</strong></td>
                            <td>{{ $log->ip_address }}</td>
                        </tr>
                        <tr>
                            <td><strong>Created At:</strong></td>
                            <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Error Information -->
        @if($log->error_message)
        <div class="col-md-6">
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Error Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <strong>Error Message:</strong><br>
                        {{ $log->error_message }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Request Data -->
    @if($log->request_data)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-arrow-up"></i> Request Data</h5>
        </div>
        <div class="card-body">
            <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->request_data, JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>
    @endif

    <!-- Response Data -->
    @if($log->response_data)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-arrow-down"></i> Response Data</h5>
        </div>
        <div class="card-body">
            <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->response_data, JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>
    @endif

    <!-- User Agent -->
    @if($log->user_agent)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user"></i> User Agent</h5>
        </div>
        <div class="card-body">
            <code>{{ $log->user_agent }}</code>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="d-flex gap-2">
        <a href="{{ route('admin.api-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
        <form action="{{ route('admin.api-logs.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this log?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete Log
            </button>
        </form>
    </div>
</div>
@endsection 