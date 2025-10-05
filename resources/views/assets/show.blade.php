@extends('layouts.app')

@section('title', 'Chi tiết tài sản - Livespo Asset Management')
@section('page-title', 'Chi tiết tài sản: ' . $asset->asset_code)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>
                    Thông tin tài sản
                </h6>
                <div>
                    @if(auth()->user()->canManageAssets())
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    @endif
                    <a href="{{ route('assets.qr', $asset) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-qrcode me-2"></i>QR Code
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Mã tài sản:</strong></td>
                                <td>{{ $asset->asset_code }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tên tài sản:</strong></td>
                                <td>{{ $asset->asset_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Danh mục:</strong></td>
                                <td><span class="badge bg-info">{{ $asset->category->name }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Thương hiệu:</strong></td>
                                <td>{{ $asset->brand ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Model:</strong></td>
                                <td>{{ $asset->model ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Số serial:</strong></td>
                                <td>{{ $asset->serial_number ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Ngày mua:</strong></td>
                                <td>{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Giá mua:</strong></td>
                                <td>{{ $asset->purchase_price ? number_format($asset->purchase_price) . ' VNĐ' : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Hết hạn bảo hành:</strong></td>
                                <td>{{ $asset->warranty_expiry ? $asset->warranty_expiry->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tình trạng:</strong></td>
                                <td>
                                    @switch($asset->condition_status)
                                        @case('new')
                                            <span class="badge bg-success">Mới</span>
                                            @break
                                        @case('good')
                                            <span class="badge bg-primary">Tốt</span>
                                            @break
                                        @case('fair')
                                            <span class="badge bg-warning">Khá</span>
                                            @break
                                        @case('poor')
                                            <span class="badge bg-danger">Kém</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $asset->condition_status }}</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    @switch($asset->status)
                                        @case('available')
                                            <span class="badge bg-success">Khả dụng</span>
                                            @break
                                        @case('assigned')
                                            <span class="badge bg-primary">Đã giao</span>
                                            @break
                                        @case('maintenance')
                                            <span class="badge bg-warning">Bảo trì</span>
                                            @break
                                        @case('retired')
                                            <span class="badge bg-danger">Ngừng sử dụng</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $asset->status }}</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Vị trí:</strong></td>
                                <td>{{ $asset->location ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment History -->
        @if($asset->assignments->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history me-2"></i>
                    Lịch sử giao tài sản
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Ngày giao</th>
                                <th>Ngày trả dự kiến</th>
                                <th>Ngày trả thực tế</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asset->assignments as $assignment)
                            <tr>
                                <td>{{ $assignment->employee->full_name }}</td>
                                <td>{{ $assignment->assigned_date->format('d/m/Y') }}</td>
                                <td>{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('d/m/Y') : '-' }}</td>
                                <td>{{ $assignment->actual_return_date ? $assignment->actual_return_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($assignment->status == 'active')
                                        <span class="badge bg-success">Đang sử dụng</span>
                                    @else
                                        <span class="badge bg-secondary">Đã trả</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- History Log -->
        @if($asset->histories->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-2"></i>
                    Lịch sử thay đổi
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Hành động</th>
                                <th>Người thực hiện</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asset->histories->sortByDesc('created_at') as $history)
                            <tr>
                                <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @switch($history->action_type)
                                        @case('created')
                                            <span class="badge bg-success">Tạo mới</span>
                                            @break
                                        @case('updated')
                                            <span class="badge bg-warning">Cập nhật</span>
                                            @break
                                        @case('assigned')
                                            <span class="badge bg-info">Giao tài sản</span>
                                            @break
                                        @case('returned')
                                            <span class="badge bg-primary">Thu hồi</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $history->action_type }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $history->performedBy->full_name ?? '-' }}</td>
                                <td>{{ $history->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- QR Code Preview -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-qrcode me-2"></i>
                    QR Code
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img src="{{config('app.url_link_qr')}}/{{$asset->filename}}" alt="qrcode">
                </div>
                <p class="text-muted">{{ $asset->qr_code }}</p>
                <a href="{{ route('assets.qr', $asset) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-expand me-2"></i>Xem lớn
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>
                    Thao tác nhanh
                </h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->canManageAssets())
                <div class="d-grid gap-2">
                    @if($asset->status == 'available')
                    <a href="{{ route('assignments.create', ['asset_id' => $asset->id]) }}" class="btn btn-success">
                        <i class="fas fa-user-plus me-2"></i>Giao tài sản
                    </a>
                    @elseif($asset->status == 'assigned')
                    <button class="btn btn-warning" onclick="returnAsset()">
                        <i class="fas fa-undo me-2"></i>Thu hồi tài sản
                    </button>
                    @endif

                    <a href="{{ route('incidents.create', ['asset_id' => $asset->id]) }}" class="btn btn-info">
                        <i class="fas fa-exclamation-triangle me-2"></i>Báo cáo sự cố
                    </a>

                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                </div>
                @endif

                <hr>

                <div class="d-grid">
                    <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function assignAsset() {
    alert('Chức năng giao tài sản sẽ được phát triển trong phiên bản tiếp theo.');
}

function returnAsset() {
    alert('Chức năng thu hồi tài sản sẽ được phát triển trong phiên bản tiếp theo.');
}
</script>
@endsection
@endsection
