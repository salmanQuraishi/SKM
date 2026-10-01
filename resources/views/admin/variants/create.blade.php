@extends('layouts.admin')

@section('title', 'Add Car Variant - AutoLogos')
@section('page_title', 'Create Car Variant')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Add New Car Variant</h5>
            <p class="text-muted small mb-0">Define a trim or variant under a company and car model.</p>
        </div>
        <a href="{{ route('admin.variants.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Variants
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following:</h6>
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-car-side text-danger me-2"></i> Vehicle Variant Information</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.variants.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- 1. Company / Brand Dropdown -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Company / Brand <span class="text-danger">*</span></label>
                            <select name="brand_id" id="companySelect" class="form-select" required>
                                <option value="">-- Select Company / Brand --</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" {{ old('brand_id', $selectedBrandId) == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Select the manufacturing company (e.g., Tata, Mahindra, Hyundai).</div>
                        </div>

                        <!-- 2. Car Model Dependent Dropdown -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center">
                                <span>Car Model <span class="text-danger">*</span></span>
                                <span id="modelLoadingSpinner" class="spinner-border spinner-border-sm text-danger" style="display: none;" role="status"></span>
                            </label>
                            <select name="car_model_id" id="carModelSelect" class="form-select" required>
                                <option value="">-- Select Car Model --</option>
                                @foreach($models as $m)
                                    <option value="{{ $m->id }}" {{ old('car_model_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Car models belonging to the selected company will populate here.</div>
                        </div>

                        <!-- 3. Variant Name -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Variant Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g., Punch Pure, Thar AX, Nexon Creative, Swift ZXi" required>
                            <div class="form-text small">Specific trim or model variant name.</div>
                        </div>

                        <!-- 4. Variant Image / Badge (Optional) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Variant Image / Badge (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <!-- 5. Sort Order & Status -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                            </div>
                            <div class="col-md-6 d-flex align-items-center pt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="variantStatus" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="variantStatus">Active / Published</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('admin.variants.index') }}" class="btn btn-light border px-4">Cancel</a>
                            <button type="submit" class="btn btn-theme-red px-4 fw-bold">
                                <i class="fa-solid fa-check me-2"></i> Save Variant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('companySelect');
    const carModelSelect = document.getElementById('carModelSelect');
    const spinner = document.getElementById('modelLoadingSpinner');

    if (companySelect && carModelSelect) {
        companySelect.addEventListener('change', function() {
            const brandId = this.value;
            carModelSelect.innerHTML = '<option value="">-- Select Car Model --</option>';

            if (!brandId) return;

            if (spinner) spinner.style.display = 'inline-block';

            fetch('/ajax/models-by-brand/' + brandId)
                .then(res => res.json())
                .then(data => {
                    if (spinner) spinner.style.display = 'none';
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            opt.textContent = m.name;
                            carModelSelect.appendChild(opt);
                        });
                    } else {
                        const opt = document.createElement('option');
                        opt.value = "";
                        opt.textContent = "-- No car models found for this brand --";
                        carModelSelect.appendChild(opt);
                    }
                })
                .catch(err => {
                    if (spinner) spinner.style.display = 'none';
                    console.error('Error fetching car models:', err);
                });
        });
    }
});
</script>
@endpush
@endsection
