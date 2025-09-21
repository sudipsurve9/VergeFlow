@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card" role="region" aria-label="Categories management">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Categories</h3>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary" aria-label="Add new category">
                            <i class="fas fa-plus"></i> Add Category
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert" aria-label="Success message">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" aria-label="Close alert">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert" aria-label="Error message">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" aria-label="Close alert">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" role="table" aria-label="Categories table">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="100">Image</th>
                                    <th>Name</th>
                                    <th>Parent</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>
                                            @if($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}" 
                                                     alt="{{ $category->name }}" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 60px; max-height: 60px;" aria-label="Category image for {{ $category->name }}">
                                            @else
                                                <div class="bg-light text-center p-2" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->description)
                                                <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($category->parent)
                                                <span class="badge badge-info">{{ $category->parent->name }}</span>
                                            @else
                                                <span class="badge badge-secondary">Root</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $category->products_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($category->id)
                                                    <a href="{{ route('admin.categories.show', $category->id) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="View" aria-label="View {{ $category->name }} details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-info" disabled title="Category ID missing">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
                                                @if($category->id)
                                                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Edit" aria-label="Edit {{ $category->name }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-warning" disabled title="Category ID missing">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endif
                                                @if($category->id)
                                                    <form action="{{ route('admin.categories.toggle-status', $category->id) }}" 
                                                          method="POST" 
                                                          style="display: inline;" aria-label="Toggle status for {{ $category->name }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" 
                                                                class="btn btn-sm {{ $category->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                                                title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}" aria-label="{{ $category->is_active ? 'Deactivate' : 'Activate' }} {{ $category->name }}">
                                                            <i class="fas {{ $category->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled title="Category ID missing">
                                                        <i class="fas fa-toggle-off"></i>
                                                    </button>
                                                @endif
                                                @if($category->id)
                                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to delete this category?')" aria-label="Delete {{ $category->name }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Delete" aria-label="Delete {{ $category->name }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-danger" disabled title="Category ID missing">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No categories found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $categories->links() }}
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
    });
</script>
@endpush 
