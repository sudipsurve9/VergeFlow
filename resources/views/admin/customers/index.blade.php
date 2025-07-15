@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Customers</h3>
                        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Customer
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="search" placeholder="Search customers...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="status-filter">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="gender-filter">
                                <option value="">All Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="date-from" placeholder="From Date">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary" id="clear-filters">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="customers-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->first_name . ' ' . $customer->last_name) }}&background=FFB300&color=fff&size=48" 
                                                     alt="Avatar" class="rounded-circle mr-3" width="40" height="40">
                                                <div>
                                                    <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>
                                                    @if($customer->date_of_birth)
                                                        <br><small class="text-muted">{{ $customer->date_of_birth->format('M d, Y') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $customer->user->email }}</strong>
                                                @if($customer->phone)
                                                    <br><small class="text-muted">{{ $customer->phone }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $customer->orders->count() }} orders</span>
                                            @if($customer->orders->count() > 0)
                                                <br><small class="text-muted">Last: {{ $customer->orders->sortByDesc('created_at')->first()->created_at->format('M d, Y') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $totalSpent = $customer->orders->where('status', '!=', 'cancelled')->sum('total_amount');
                                            @endphp
                                            <strong>₹{{ number_format($totalSpent, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($customer->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.customers.show', $customer) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.customers.edit', $customer) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Edit Customer">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.customers.toggle-status', $customer) }}" 
                                                      method="POST" 
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="btn btn-sm {{ $customer->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                                            title="{{ $customer->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas {{ $customer->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.customers.reset-password', $customer) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Reset password to default (password123)?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-warning" 
                                                            title="Reset Password">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.customers.destroy', $customer) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No customers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Search functionality
        $('#search').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#customers-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Status filter
        $('#status-filter').change(function() {
            const status = $(this).val();
            if (status !== '') {
                $('#customers-table tbody tr').hide();
                $('#customers-table tbody tr').each(function() {
                    const isActive = $(this).find('td:nth-child(5) .badge').hasClass('badge-success');
                    if ((status === '1' && isActive) || (status === '0' && !isActive)) {
                        $(this).show();
                    }
                });
            } else {
                $('#customers-table tbody tr').show();
            }
        });

        // Gender filter
        $('#gender-filter').change(function() {
            const gender = $(this).val().toLowerCase();
            if (gender) {
                $('#customers-table tbody tr').hide();
                $('#customers-table tbody tr').each(function() {
                    const rowText = $(this).text().toLowerCase();
                    if (rowText.includes(gender)) {
                        $(this).show();
                    }
                });
            } else {
                $('#customers-table tbody tr').show();
            }
        });

        // Date filter
        function filterByDate() {
            const fromDate = $('#date-from').val();
            
            if (fromDate) {
                $('#customers-table tbody tr').hide();
                $('#customers-table tbody tr').each(function() {
                    const joinedDate = $(this).find('td:nth-child(6)').text();
                    const date = new Date(joinedDate);
                    const from = new Date(fromDate);
                    
                    if (date >= from) {
                        $(this).show();
                    }
                });
            } else {
                $('#customers-table tbody tr').show();
            }
        }

        $('#date-from').change(filterByDate);

        // Clear filters
        $('#clear-filters').click(function() {
            $('#search').val('');
            $('#status-filter').val('');
            $('#gender-filter').val('');
            $('#date-from').val('');
            $('#customers-table tbody tr').show();
        });
    });
</script>
@endpush 