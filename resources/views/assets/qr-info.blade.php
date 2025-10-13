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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .asset-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .asset-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
            position: relative;
        }

        .asset-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="50" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="30" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .asset-code {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .asset-name {
            font-size: 1.1rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            font-weight: 300;
        }

        .info-section {
            padding: 25px 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .info-item:hover {
            background: rgba(102, 126, 234, 0.02);
            border-radius: 8px;
            padding: 12px 10px;
            margin: 0 -10px;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #4a5568;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .info-label i {
            margin-right: 12px;
            width: 18px;
            text-align: center;
            color: #667eea;
            font-size: 1rem;
        }

        .info-value {
            font-weight: 500;
            color: #2d3748;
            text-align: right;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-available {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            box-shadow: 0 2px 4px rgba(21, 87, 36, 0.2);
        }

        .status-assigned {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            box-shadow: 0 2px 4px rgba(133, 100, 4, 0.2);
        }

        .status-maintenance {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            box-shadow: 0 2px 4px rgba(114, 28, 36, 0.2);
        }

        .status-retired {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            box-shadow: 0 2px 4px rgba(12, 84, 96, 0.2);
        }

        .assignment-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .assignment-info h6 {
            color: #495057;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .assignment-info h6 i {
            color: #667eea;
            margin-right: 8px;
        }

        .livespo-logo {
            text-align: center;
            margin-top: 25px;
            padding: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(102, 126, 234, 0.02);
        }

        .livespo-logo h5 {
            color: #667eea;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .livespo-logo p {
            color: #6c757d;
            font-size: 0.85rem;
            margin: 0;
        }

        .badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .history-item, .incident-item {
            transition: all 0.2s ease;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 8px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .history-item:hover, .incident-item:hover {
            background-color: rgba(102, 126, 234, 0.05);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding: 20px;
            background: rgba(248, 249, 250, 0.5);
            border-radius: 15px;
        }

        .btn-back {
            flex: 1;
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(108, 117, 125, 0.3);
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #495057, #343a40);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(108, 117, 125, 0.4);
            color: white;
        }

        .btn-support {
            flex: 1;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.3);
        }

        .btn-support:hover {
            background: linear-gradient(135deg, #20c997, #17a2b8);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .update-time {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            font-size: 0.8rem;
            color: #6c757d;
        }

        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-info { color: #17a2b8 !important; }
        .text-secondary { color: #6c757d !important; }
        
        .bg-success { background-color: #28a745 !important; }
        .bg-warning { background-color: #ffc107 !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .bg-info { background-color: #17a2b8 !important; }
        .bg-secondary { background-color: #6c757d !important; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="asset-card">
                <!-- Asset Header -->
                <div class="asset-header">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 15px; position: relative; z-index: 1;">
                        <img style="width: 60px; height: 60px; object-fit: contain; filter: brightness(0) invert(1);" 
                             src="{{asset('images/logo/logo.png')}}" alt="Livespo Logo">
                        <div style="text-align: left;">
                            <div class="asset-code">{{ $asset->asset_code }}</div>
                            <div class="asset-name">{{ $asset->asset_name }}</div>
                        </div>
                    </div>
                </div>
                <!-- Asset Information -->
                <div class="info-section">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-qrcode"></i>
                            Mã tài sản
                        </div>
                        <div class="info-value">{{ $asset->asset_code }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-laptop"></i>
                            Tên tài sản
                        </div>
                        <div class="info-value">{{ $asset->asset_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-layer-group"></i>
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
                <div class="action-buttons">
                    <button onclick="window.history.back()" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </button>
                    <a href="tel:+84123456789" class="btn-support">
                        <i class="fas fa-phone me-2"></i>Hỗ trợ
                    </a>
                </div>
                
                <div class="update-time">
                    <i class="fas fa-clock me-1"></i>
                    Cập nhật lần cuối: {{ $asset->updated_at->format('d/m/Y H:i') }}
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
