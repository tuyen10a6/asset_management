<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài sản - {{ $asset->asset_code }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: white;
            min-height: 100vh;
            padding: 20px 0;
        }

        .asset-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .asset-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .asset-code {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .asset-name {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .info-section {
            padding: 30px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .info-value {
            font-weight: 500;
            color: #333;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-assigned {
            background: #fff3cd;
            color: #856404;
        }

        .status-maintenance {
            background: #f8d7da;
            color: #721c24;
        }

        .status-retired {
            background: #d1ecf1;
            color: #0c5460;
        }

        .assignment-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .livespo-logo {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .livespo-logo h5 {
            color: #667eea;
            font-weight: bold;
        }

        .badge {
            font-size: 0.8rem;
        }

        .history-item, .incident-item {
            transition: background-color 0.2s;
        }

        .history-item:hover, .incident-item:hover {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 5px;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-info {
            background-color: #17a2b8 !important;
        }

        .bg-secondary {
            background-color: #6c757d !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="asset-card">
                <!-- Asset Header -->
                <div style="display: flex" class="asset-header">
                    <img style="width: 200px" src="{{asset('images/logo/logo.png')}}" alt="logo"
                         class="img-size-50 mr-3 img-circle">
                </div>
                <!-- Asset Information -->
                <div class="info-section">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-tag"></i>
                            Mã tài sản
                        </div>
                        <div class="info-value">{{ $asset->asset_code }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-tag"></i>
                            Tên tài sản
                        </div>
                        <div class="info-value">{{ $asset->asset_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-tag"></i>
                            Danh mục
                        </div>
                        <div class="info-value">{{ $asset->category->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-industry"></i>
                            Thương hiệu
                        </div>
                        <div class="info-value">{{ $asset->brand ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-cube"></i>
                            Model
                        </div>
                        <div class="info-value">{{ $asset->model ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-barcode"></i>
                            Serial Number
                        </div>
                        <div class="info-value">{{ $asset->serial_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-info-circle"></i>
                            Trạng thái
                        </div>
                        <div class="info-value">
                            @switch($asset->status)
                                @case('available')
                                    <span class="status-badge status-available">Khả dụng</span>
                                    @break
                                @case('assigned')
                                    <span class="status-badge status-assigned">Đã cấp phát</span>
                                    @break
                                @case('maintenance')
                                    <span class="status-badge status-maintenance">Bảo trì</span>
                                    @break
                                @case('retired')
                                    <span class="status-badge status-retired">Ngừng sử dụng</span>
                                    @break
                                @default
                                    <span class="status-badge">{{ $asset->status }}</span>
                            @endswitch
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Vị trí
                        </div>
                        <div class="info-value">{{ $asset->location ?? 'N/A' }}</div>
                    </div>

                    @if($asset->condition_status)
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-clipboard-check"></i>
                                Tình trạng
                            </div>
                            <div class="info-value">
                                @switch($asset->condition_status)
                                    @case('excellent')
                                        <span class="badge bg-success">Tốt</span>
                                        @break
                                    @case('good')
                                        <span class="badge bg-info">Khá</span>
                                        @break
                                    @case('fair')
                                        <span class="badge bg-warning">Trung bình</span>
                                        @break
                                    @case('poor')
                                        <span class="badge bg-danger">Kém</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $asset->condition_status }}</span>
                                @endswitch
                            </div>
                        </div>
                    @endif

                    @if($asset->purchase_price)
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-dollar-sign"></i>
                                Giá mua
                            </div>
                            <div class="info-value">{{ number_format($asset->purchase_price, 0, ',', '.') }} VNĐ</div>
                        </div>
                    @endif

                    @if($asset->purchase_date)
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar"></i>
                                Ngày mua
                            </div>
                            <div class="info-value">{{ $asset->purchase_date->format('d/m/Y') }}</div>
                        </div>
                    @endif

                    @if($asset->warranty_expiry)
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-shield-alt"></i>
                                Bảo hành đến
                            </div>
                            <div class="info-value">
                                {{ $asset->warranty_expiry->format('d/m/Y') }}
                                @if($asset->warranty_expiry->isPast())
                                    <small class="text-danger">(Hết hạn)</small>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Assignment Information -->
                    @if($asset->currentAssignment)
                        <div class="assignment-info">
                            <h6 class="mb-3"><i class="fas fa-user me-2"></i>Thông tin cấp phát</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Nhân viên:</strong><br>
                                    {{ $asset->currentAssignment->employee->full_name }}<br>
                                    <small
                                        class="text-muted">{{ $asset->currentAssignment->employee->employee_code }}</small>
                                </div>
                                <div class="col-6">
                                    <strong>Bộ phận:</strong><br>
                                    {{ $asset->currentAssignment->employee->department->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Ngày cấp phát:</strong><br>
                                    {{ $asset->currentAssignment->assigned_date->format('d/m/Y') }}
                                </div>
                                @if($asset->currentAssignment->expected_return_date)
                                    <div class="col-6">
                                        <strong>Dự kiến thu hồi:</strong><br>
                                        {{ $asset->currentAssignment->expected_return_date->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                            @if($asset->currentAssignment->assignment_notes)
                                <div class="mt-2">
                                    <strong>Ghi chú:</strong><br>
                                    <small class="text-muted">{{ $asset->currentAssignment->assignment_notes }}</small>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Recent History -->
                    @if($asset->histories && $asset->histories->count() > 0)
                        <div class="assignment-info">
                            <h6 class="mb-3"><i class="fas fa-history me-2"></i>Lịch sử gần đây</h6>
                            @foreach($asset->histories->take(3) as $history)
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 history-item"
                                     style="border-bottom: 1px solid #eee;">
                                    <div>
                                        <strong>
                                            @switch($history->action_type)
                                                @case('created')
                                                    <i class="fas fa-plus-circle text-success me-1"></i>Tạo mới
                                                    @break
                                                @case('updated')
                                                    <i class="fas fa-edit text-warning me-1"></i>Cập nhật
                                                    @break
                                                @case('assigned')
                                                    <i class="fas fa-user-plus text-info me-1"></i>Cấp phát
                                                    @break
                                                @case('returned')
                                                    <i class="fas fa-user-minus text-secondary me-1"></i>Thu hồi
                                                    @break
                                                @default
                                                    <i class="fas fa-circle text-muted me-1"></i>{{ ucfirst($history->action_type) }}
                                            @endswitch
                                        </strong>
                                        @if($history->notes)
                                            <br><small class="text-muted">{{ $history->notes }}</small>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $history->action_date->format('d/m/Y H:i') }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Recent Incidents -->
                    @if($asset->incidentReports && $asset->incidentReports->count() > 0)
                        <div class="assignment-info">
                            <h6 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Sự cố gần đây</h6>
                            @foreach($asset->incidentReports->take(2) as $incident)
                                <div class="d-flex justify-content-between align-items-start mb-2 pb-2 incident-item"
                                     style="border-bottom: 1px solid #eee;">
                                    <div class="flex-grow-1">
                                        <strong>
                                            @switch($incident->incident_type)
                                                @case('damage')
                                                    <i class="fas fa-tools text-danger me-1"></i>Hư hỏng
                                                    @break
                                                @case('maintenance')
                                                    <i class="fas fa-wrench text-warning me-1"></i>Bảo trì
                                                    @break
                                                @case('lost')
                                                    <i class="fas fa-search text-danger me-1"></i>Mất tích
                                                    @break
                                                @default
                                                    <i class="fas fa-exclamation-circle text-warning me-1"></i>{{ ucfirst($incident->incident_type) }}
                                            @endswitch
                                        </strong>
                                        <span class="badge badge-sm ms-2
                                        @if($incident->status == 'resolved') bg-success
                                        @elseif($incident->status == 'in_progress') bg-warning
                                        @else bg-danger @endif">
                                        {{ ucfirst($incident->status) }}
                                    </span>
                                        @if($incident->description)
                                            <br><small
                                                class="text-muted">{{ Str::limit($incident->description, 60) }}</small>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $incident->incident_date->format('d/m/Y') }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="text-center py-3" style="border-top: 1px solid #eee;">
                    <div class="row p-3">
                        <div class="col-6">
                            <button onclick="window.history.back()" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </button>
                        </div>
                        <div class="col-6">
                            <a href="tel:+84123456789" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-phone me-2"></i>Hỗ trợ
                            </a>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Cập nhật lần cuối: {{ $asset->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>

                <!-- Livespo Logo -->
                <div class="livespo-logo">
                    <h5><i class="fas fa-boxes me-2"></i>LIVESPO ASSET MANAGEMENT</h5>
                    <p class="text-muted mb-0">Hệ thống quản lý tài sản nội bộ</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
