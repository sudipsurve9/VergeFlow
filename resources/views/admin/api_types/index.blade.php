@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">API Types</h1>
        <a href="{{ route('admin.api-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add API Type
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Icon</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $type)
                        <tr>
                            <td>{{ ucfirst($type->name) }}</td>
                            <td>@if($type->icon) <i class="{{ $type->icon }}"></i> @endif {{ $type->icon }}</td>
                            <td>{{ $type->description }}</td>
                            <td>
                                <a href="{{ route('admin.api-types.edit', $type->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.api-types.destroy', $type->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this API type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No API types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-4">
                {{ $types->links() }}
            </div>
        </div>
 