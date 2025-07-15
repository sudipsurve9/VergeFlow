@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">API Integrations</h1>
        <a href="{{ route('admin.api-integrations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Integration
        </a>
    </div>
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search integrations..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            @php $grouped = $integrations->groupBy('type'); @endphp
            @foreach($grouped as $type => $group)
                <h5 class="mb-3 mt-4"><i class="fas fa-plug me-2"></i>{{ ucfirst($type) }}</h5>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>cURL Command</th>
                            <th>Last Updated By</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $integration)
                            <tr>
                                <td>
                                    @if(strtolower($integration->type) == 'shiprocket')
                                        <i class="fas fa-rocket text-primary" title="Shiprocket"></i>
                                    @elseif(strtolower($integration->type) == 'delhivery')
                                        <i class="fas fa-truck text-success" title="Delhivery"></i>
                                    @else
                                        <i class="fas fa-plug text-secondary" title="API"></i>
                                    @endif
                                    {{ ucfirst($integration->type) }}
                                </td>
                                <td>{{ $integration->email }}</td>
                                <td>@if($integration->password) •••• @endif</td>
                                <td>{{ $integration->curl_command }}</td>
                                <td>{{ $integration->updated_by ?? 'N/A' }}</td>
                                <td>{{ $integration->updated_at ? $integration->updated_at->format('Y-m-d H:i') : '' }}</td>
                                <td>
                                    <a href="{{ route('admin.api-integrations.edit', $integration->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.api-integrations.destroy', $integration->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this integration?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
            <div class="d-flex justify-content-center mt-4">
                {{ $integrations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 