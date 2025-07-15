@extends('layouts.super_admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Manage Clients</h1>
                <a href="{{ route('super_admin.clients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Client
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Domain</th>
                                <th>Stats</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>
                                    <strong>{{ $client->name }}</strong>
                                    @if($client->theme)
                                        <br><small class="text-muted">Theme: {{ $client->theme }}</small>
                                    @endif
                                </td>
                                <td>{{ $client->company_name }}</td>
                                <td>
                                    <div>{{ $client->contact_email }}</div>
                                    @if($client->contact_phone)
                                        <small class="text-muted">{{ $client->contact_phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($client->domain)
                                        <a href="http://{{ $client->domain }}" target="_blank">{{ $client->domain }}</a>
                                    @elseif($client->subdomain)
                                        <a href="http://{{ $client->subdomain }}.vault64.com" target="_blank">{{ $client->subdomain }}.vault64.com</a>
                                    @else
                                        <span class="text-muted">No domain</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="text-xs font-weight-bold text-primary">{{ $client->users_count }}</div>
                                            <small class="text-muted">Users</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-xs font-weight-bold text-success">{{ $client->products_count }}</div>
                                            <small class="text-muted">Products</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-xs font-weight-bold text-info">{{ $client->orders_count }}</div>
                                            <small class="text-muted">Orders</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $client->is_active ? 'success' : 'danger' }}">
                                        {{ $client->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $client->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('super_admin.clients.edit', $client) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteClient({{ $client->id }})" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $clients->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No clients found</h4>
                    <p class="text-muted">Get started by creating your first client.</p>
                    <a href="{{ route('super_admin.clients.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Client
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteClientModal" tabindex="-1" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteClientModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this client? This action cannot be undone and will delete all associated data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteClientForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Client</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteClient(clientId) {
    if (confirm('Are you sure you want to delete this client? This will delete ALL associated data including users, products, orders, etc.')) {
        const form = document.getElementById('deleteClientForm');
        form.action = `/super-admin/clients/${clientId}`;
        form.submit();
    }
}
</script>
@endsection 