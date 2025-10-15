@extends('layouts.app')

@section('title', 'Quản lý cấp phát tài sản')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-handshake me-2"></i>Quản lý cấp phát tài sản</h2>
    <a href="{{ route('assignments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Cấp phát mới
    </a>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('assignments.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo mã tài sản, tên nhân viên..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang cấp phát</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Đã thu hồi</option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Mất</option>
                        <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Hỏng</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="department_id" class="form-select">
                        <option value="">Tất cả bộ phận</option>
                        <!-- Will be populated dynamically -->
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-2"></i>Tìm kiếm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Assignments Table -->
<div class="card">
    <div class="card-body">
        @if($assignments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mã tài sản</th>
                            <th>Tên tài sản</th>
                            <th>Nhân viên</th>
                            <th>Bộ phận</th>
                            <th>Ngày cấp phát</th>
                            <th>Ngày dự kiến thu hồi</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                            <tr>
                                <td><strong>{{ $assignment->asset->asset_code }}</strong></td>
                                <td>{{ $assignment->asset->asset_name }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $assignment->employee->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $assignment->employee->employee_code }}</small>
                                    </div>
                                </td>
                                <td>{{ $assignment->employee->department->name ?? 'N/A' }}</td>
                                <td>{{ $assignment->assigned_date->format('d/m/Y') }}</td>
                                <td>{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @switch($assignment->status)
                                        @case('active')
                                            <span class="badge bg-success">Đang cấp phát</span>
                                            @if($assignment->expected_return_date && $assignment->expected_return_date->isPast())
                                                <br><small class="text-danger">Quá hạn</small>
                                            @endif
                                            @break
                                        @case('returned')
                                            <span class="badge bg-info">Đã thu hồi</span>
                                            @break
                                        @case('lost')
                                            <span class="badge bg-danger">Mất</span>
                                            @break
                                        @case('damaged')
                                            <span class="badge bg-warning">Hỏng</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($assignment->status === 'active')
                                            <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('assignments.return.form', $assignment) }}" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $assignments->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy cấp phát nào</h5>
                <p class="text-muted">Hãy thử thay đổi điều kiện tìm kiếm hoặc tạo cấp phát mới.</p>
            </div>
        @endif
    </div>
</div>

<!-- Expiring Assignments Alert -->
<div class="mt-4">
    <div class="card border-warning">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Cấp phát sắp hết hạn</h6>
        </div>
        <div class="card-body" id="expiring-assignments">
            <div class="text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Đang tải...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load expiring assignments
    fetch('{{ route("assignments.expiring") }}')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('expiring-assignments');
            if (data.length === 0) {
                container.innerHTML = '<p class="text-muted mb-0">Không có cấp phát nào sắp hết hạn.</p>';
            } else {
                let html = '<div class="row">';
                data.forEach(assignment => {
                    const daysLeft = Math.ceil((new Date(assignment.expected_return_date) - new Date()) / (1000 * 60 * 60 * 24));
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${assignment.asset.asset_code}</strong> - ${assignment.employee.full_name}
                                    <br>
                                    <small class="text-muted">Hết hạn: ${new Date(assignment.expected_return_date).toLocaleDateString('vi-VN')}</small>
                                </div>
                                <span class="badge ${daysLeft <= 7 ? 'bg-danger' : 'bg-warning'}">${daysLeft} ngày</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error loading expiring assignments:', error);
            document.getElementById('expiring-assignments').innerHTML = '<p class="text-danger mb-0">Lỗi khi tải dữ liệu.</p>';
        });
});
</script>
@endpush
@endsection
