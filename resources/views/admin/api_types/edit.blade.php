@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Edit API Type</h1>
        <a href="{{ route('admin.api-types.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.api-types.update', $type->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $type->name }}" required>
                </div>
                <div class="mb-3">
                    <label for="icon" class="form-label">Icon <small>(FontAwesome class, e.g. fas fa-rocket)</small></label>
                    <input type="text" class="form-control" id="icon" name="icon" value="{{ $type->icon }}">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" class="form-control" id="description" name="description" value="{{ $type->description }}">
                </div>
                <button type="submit" class="btn btn-primary">Update API Type</button>
            </form>
        </div>
    </div>
</div>
@endsection 