@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Create Product</h3>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name') }}" 
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" 
                                                      name="description" 
                                                      rows="4">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sku">SKU</label>
                                                    <input type="text" 
                                                           class="form-control @error('sku') is-invalid @enderror" 
                                                           id="sku" 
                                                           name="sku" 
                                                           value="{{ old('sku') }}">
                                                    @error('sku')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="barcode">Barcode</label>
                                                    <input type="text" 
                                                           class="form-control @error('barcode') is-invalid @enderror" 
                                                           id="barcode" 
                                                           name="barcode" 
                                                           value="{{ old('barcode') }}">
                                                    @error('barcode')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Pricing</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price">Price <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">₹</span>
                                                        </div>
                                                        <input type="number" 
                                                               class="form-control @error('price') is-invalid @enderror" 
                                                               id="price" 
                                                               name="price" 
                                                               value="{{ old('price') }}" 
                                                               step="0.01" 
                                                               min="0" 
                                                               required>
                                                    </div>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="compare_price">Compare Price</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">₹</span>
                                                        </div>
                                                        <input type="number" 
                                                               class="form-control @error('compare_price') is-invalid @enderror" 
                                                               id="compare_price" 
                                                               name="compare_price" 
                                                               value="{{ old('compare_price') }}" 
                                                               step="0.01" 
                                                               min="0">
                                                    </div>
                                                    @error('compare_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cost_price">Cost Price</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">₹</span>
                                                        </div>
                                                        <input type="number" 
                                                               class="form-control @error('cost_price') is-invalid @enderror" 
                                                               id="cost_price" 
                                                               name="cost_price" 
                                                               value="{{ old('cost_price') }}" 
                                                               step="0.01" 
                                                               min="0">
                                                    </div>
                                                    @error('cost_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Inventory</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stock_quantity">Stock Quantity <span class="text-danger">*</span></label>
                                                    <input type="number" 
                                                           class="form-control @error('stock_quantity') is-invalid @enderror" 
                                                           id="stock_quantity" 
                                                           name="stock_quantity" 
                                                           value="{{ old('stock_quantity', 0) }}" 
                                                           min="0" 
                                                           required>
                                                    @error('stock_quantity')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="low_stock_threshold">Low Stock Threshold</label>
                                                    <input type="number" 
                                                           class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                                           id="low_stock_threshold" 
                                                           name="low_stock_threshold" 
                                                           value="{{ old('low_stock_threshold', 5) }}" 
                                                           min="0">
                                                    @error('low_stock_threshold')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEO -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">SEO Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="meta_title">Meta Title</label>
                                            <input type="text" 
                                                   class="form-control @error('meta_title') is-invalid @enderror" 
                                                   id="meta_title" 
                                                   name="meta_title" 
                                                   value="{{ old('meta_title') }}">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="meta_description">Meta Description</label>
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                      id="meta_description" 
                                                      name="meta_description" 
                                                      rows="3">{{ old('meta_description') }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="meta_keywords">Meta Keywords</label>
                                            <input type="text" 
                                                   class="form-control @error('meta_keywords') is-invalid @enderror" 
                                                   id="meta_keywords" 
                                                   name="meta_keywords" 
                                                   value="{{ old('meta_keywords') }}"
                                                   placeholder="keyword1, keyword2, keyword3">
                                            @error('meta_keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Category Selection -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Category</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="category_id">Category <span class="text-danger">*</span></label>
                                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                                    id="category_id" 
                                                    name="category_id" 
                                                    required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Images -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Product Images</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="images">Product Images</label>
                                            <div class="custom-file">
                                                <input type="file" 
                                                       class="custom-file-input @error('images.*') is-invalid @enderror" 
                                                       id="images" 
                                                       name="images[]" 
                                                       accept="image/*" 
                                                       multiple>
                                                <label class="custom-file-label" for="images">Choose files</label>
                                            </div>
                                            @error('images.*')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                You can select multiple images. First image will be the main product image.
                                            </small>
                                        </div>

                                        <div id="image-preview-container" class="mt-3">
                                            <!-- Image previews will be shown here -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Status -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Product Status</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" 
                                                       class="custom-control-input" 
                                                       id="is_active" 
                                                       name="is_active" 
                                                       value="1" 
                                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_active">Active</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" 
                                                       class="custom-control-input" 
                                                       id="is_featured" 
                                                       name="is_featured" 
                                                       value="1" 
                                                       {{ old('is_featured') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_featured">Featured Product</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Dimensions -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Product Dimensions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="weight">Weight (kg)</label>
                                            <input type="number" 
                                                   class="form-control @error('weight') is-invalid @enderror" 
                                                   id="weight" 
                                                   name="weight" 
                                                   value="{{ old('weight') }}" 
                                                   step="0.01" 
                                                   min="0">
                                            @error('weight')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="dimensions">Dimensions</label>
                                            <input type="text" 
                                                   class="form-control @error('dimensions') is-invalid @enderror" 
                                                   id="dimensions" 
                                                   name="dimensions" 
                                                   value="{{ old('dimensions') }}"
                                                   placeholder="L x W x H (cm)">
                                            @error('dimensions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Product
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // File input change handler
        $('#images').change(function() {
            const files = this.files;
            const container = $('#image-preview-container');
            container.empty();
            
            if (files.length > 0) {
                // Update label
                $(this).next('.custom-file-label').text(files.length + ' file(s) selected');
                
                // Show previews
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = $(`
                            <div class="mb-2">
                                <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 100px;">
                                <small class="d-block text-muted">${file.name}</small>
                            </div>
                        `);
                        container.append(preview);
                    }
                    
                    reader.readAsDataURL(file);
                }
            } else {
                $(this).next('.custom-file-label').text('Choose files');
            }
        });

        // Auto-generate meta title from product name
        $('#name').on('input', function() {
            const name = $(this).val();
            if (name && !$('#meta_title').val()) {
                $('#meta_title').val(name);
            }
        });

        // Auto-generate meta description from description
        $('#description').on('input', function() {
            const description = $(this).val();
            if (description && !$('#meta_description').val()) {
                $('#meta_description').val(description.substring(0, 160));
            }
        });

        // Auto-generate SKU from product name
        $('#name').on('input', function() {
            const name = $(this).val();
            if (name && !$('#sku').val()) {
                const sku = name.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 10);
                $('#sku').val(sku);
            }
        });
    });
</script>
@endpush 