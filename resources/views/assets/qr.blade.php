@extends('layouts.app')

@section('title', 'QR Code - ' . $asset->asset_code)
@section('page-title', 'QR Code: ' . $asset->asset_code)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3 text-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-qrcode me-2"></i>
                    QR Code cho tài sản: {{ $asset->asset_name }}
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <img src="{{config('app.url_link_qr')}}/{{$asset->filename}}" alt="qrcode">
                        </div>
                        <p class="text-muted">Mã QR: {{ $asset->qr_code }}</p>

                        <div class="btn-group" role="group">
                            <button onclick="printQR()" class="btn btn-primary">
                                <i class="fas fa-print me-2"></i>In QR Code
                            </button>
                            <button onclick="downloadQR()" class="btn btn-success">
                                <i class="fas fa-download me-2"></i>Tải xuống
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Thông tin tài sản</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Mã:</strong></td>
                                        <td>{{ $asset->asset_code }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tên:</strong></td>
                                        <td>{{ $asset->asset_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Danh mục:</strong></td>
                                        <td>{{ $asset->category->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Thương hiệu:</strong></td>
                                        <td>{{ $asset->brand ?? '-' }}</td>
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

                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Hướng dẫn sử dụng:</strong><br>
                                • Quét QR code để xem thông tin chi tiết<br>
                                • In và dán lên tài sản để dễ quản lý<br>
                                • Sử dụng app di động để quét nhanh
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại chi tiết
                        </a>
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Danh sách tài sản
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Template (Hidden) -->
<div id="printTemplate" style="display: none;">
    <div style="text-align: center; padding: 20px;">
        <h2>{{ $asset->asset_name }}</h2>
        <p><strong>Mã tài sản:</strong> {{ $asset->asset_code }}</p>
        <div style="margin: 20px 0;">
            {!! $qrCode !!}
        </div>
        <p><strong>QR Code:</strong> {{ $asset->qr_code }}</p>
        <p><strong>Công ty:</strong> Livespo</p>
        <p><strong>Ngày in:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>

@section('scripts')
<script>
function printQR() {
    var printContent = document.getElementById('printTemplate').innerHTML;
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}

function downloadQR() {
    // Create a temporary link to download the SVG
    var svg = document.querySelector('svg');
    var svgData = new XMLSerializer().serializeToString(svg);
    var svgBlob = new Blob([svgData], {type: "image/svg+xml;charset=utf-8"});
    var svgUrl = URL.createObjectURL(svgBlob);

    var downloadLink = document.createElement("a");
    downloadLink.href = svgUrl;
    downloadLink.download = "{{ $asset->asset_code }}_qrcode.svg";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
@endsection
@endsection
