@extends('layouts.app')

@section('title', 'Cấp phát tài sản mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-handshake me-2"></i>Cấp phát tài sản mới</h2>
    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('assignments.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Tài sản <span class="text-danger">*</span></label>
                        <select class="form-select @error('asset_id') is-invalid @enderror"
                                id="asset_id" name="asset_id" required>
                            <option value="">Chọn tài sản</option>
                            @foreach($availableAssets as $asset)
                                <option value="{{ $asset->id }}"
                                        {{ (old('asset_id') == $asset->id || (isset($selectedAssetId) && $selectedAssetId == $asset->id)) ? 'selected' : '' }}
                                        data-category="{{ $asset->category->name }}"
                                        data-brand="{{ $asset->brand }}"
                                        data-model="{{ $asset->model }}">
                                    {{ $asset->asset_code }} - {{ $asset->asset_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="asset-info" class="mt-2" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <small>
                                        <strong>Danh mục:</strong> <span id="asset-category"></span><br>
                                        <strong>Thương hiệu:</strong> <span id="asset-brand"></span><br>
                                        <strong>Model:</strong> <span id="asset-model"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Nhân viên <span class="text-danger">*</span></label>
                        <select class="form-select @error('employee_id') is-invalid @enderror"
                                id="employee_id" name="employee_id" required>
                            <option value="">Chọn nhân viên</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}
                                        data-department="{{ $employee->department->name ?? '' }}"
                                        data-position="{{ $employee->position ?? '' }}">
                                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="employee-info" class="mt-2" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <small>
                                        <strong>Bộ phận:</strong> <span id="employee-department"></span><br>
                                        <strong>Chức vụ:</strong> <span id="employee-position"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="assigned_date" class="form-label">Ngày cấp phát <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('assigned_date') is-invalid @enderror"
                               id="assigned_date" name="assigned_date" value="{{ old('assigned_date', date('Y-m-d')) }}" required>
                        @error('assigned_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="expected_return_date" class="form-label">Ngày dự kiến thu hồi</label>
                        <input type="date" class="form-control @error('expected_return_date') is-invalid @enderror"
                               id="expected_return_date" name="expected_return_date" value="{{ old('expected_return_date') }}">
                        @error('expected_return_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Để trống nếu cấp phát vô thời hạn</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="assignment_notes" class="form-label">Ghi chú</label>
                <textarea class="form-control @error('assignment_notes') is-invalid @enderror"
                          id="assignment_notes" name="assignment_notes" rows="3">{{ old('assignment_notes') }}</textarea>
                @error('assignment_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Cấp phát
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const assetSelect = document.getElementById('asset_id');
    const employeeSelect = document.getElementById('employee_id');
    const assetInfo = document.getElementById('asset-info');
    const employeeInfo = document.getElementById('employee-info');

    // Show asset info when selected
    assetSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('asset-category').textContent = selectedOption.dataset.category || 'N/A';
            document.getElementById('asset-brand').textContent = selectedOption.dataset.brand || 'N/A';
            document.getElementById('asset-model').textContent = selectedOption.dataset.model || 'N/A';
            assetInfo.style.display = 'block';
        } else {
            assetInfo.style.display = 'none';
        }
    });

    // Show employee info when selected
    employeeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('employee-department').textContent = selectedOption.dataset.department || 'N/A';
            document.getElementById('employee-position').textContent = selectedOption.dataset.position || 'N/A';
            employeeInfo.style.display = 'block';
        } else {
            employeeInfo.style.display = 'none';
        }
    });

    // Set minimum date for expected return date
    const assignedDateInput = document.getElementById('assigned_date');
    const expectedReturnDateInput = document.getElementById('expected_return_date');

    assignedDateInput.addEventListener('change', function() {
        expectedReturnDateInput.min = this.value;
    });

    // Initialize minimum date
    if (assignedDateInput.value) {
        expectedReturnDateInput.min = assignedDateInput.value;
    }
});
</script>
@endpush
@endsection
