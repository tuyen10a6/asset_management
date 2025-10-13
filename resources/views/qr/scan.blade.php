@extends('layouts.public')

@section('title', 'Quét QR Code - Livespo Asset Management')
@section('page-title', 'Quét QR Code')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-qrcode me-2"></i>
                    Quét mã QR để xem thông tin tài sản
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="qr-reader" style="width: 100%; height: 400px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center;">
                            <div class="text-center text-muted">
                                <i class="fas fa-camera fa-3x mb-3"></i>
                                <p>Camera sẽ hiển thị ở đây</p>
                                <button id="start-scan" class="btn btn-primary">
                                    <i class="fas fa-play me-2"></i>Bắt đầu quét
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Hướng dẫn:</strong><br>
                                • Cho phép truy cập camera khi được yêu cầu<br>
                                • Đưa QR code vào khung hình<br>
                                • Hệ thống sẽ tự động nhận diện và chuyển hướng
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Tìm kiếm thủ công</h6>
                        <form id="manual-search">
                            <div class="form-group mb-3">
                                <label for="asset-code">Nhập mã tài sản hoặc QR code:</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="asset-code" 
                                       placeholder="Ví dụ: LT001, QR_LT001_123456">
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                        </form>
                        
                        <hr>
                        
                        <h6>Kết quả quét gần đây</h6>
                        <div id="recent-scans">
                            <p class="text-muted">Chưa có kết quả quét nào.</p>
                        </div>
                        
                        <hr>
                        
                        <h6>Thống kê nhanh</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5>{{ \App\Models\Asset::count() }}</h5>
                                        <small>Tổng tài sản</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5>{{ \App\Models\Asset::where('status', 'available')->count() }}</h5>
                                        <small>Khả dụng</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Result Modal -->
<div class="modal fade" id="scanResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Quét thành công!
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Đã tìm thấy tài sản. Bạn có muốn xem chi tiết?</p>
                <div id="asset-preview"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="view-asset">Xem chi tiết</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrcodeScanner = null;
let scanResultModal = new bootstrap.Modal(document.getElementById('scanResultModal'));

// Start QR Scanner
document.getElementById('start-scan').addEventListener('click', function() {
    startScanner();
});

function startScanner() {
    const qrReaderElement = document.getElementById('qr-reader');
    qrReaderElement.innerHTML = '<div id="qr-scanner"></div>';
    
    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-scanner",
        { 
            fps: 10, 
            qrbox: {width: 250, height: 250},
            aspectRatio: 1.0
        },
        false
    );
    
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
}

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code matched = ${decodedText}`, decodedResult);
    
    // Stop scanning
    html5QrcodeScanner.clear();
    
    // Process the scanned result
    processScannedCode(decodedText);
}

function onScanFailure(error) {
    // Handle scan failure - usually not necessary to do anything
    console.warn(`Code scan error = ${error}`);
}

function processScannedCode(code) {
    console.log('Processing scanned code:', code);
    
    // Check if it's a URL from our QR codes (https://qlts.livespo.vn/ASSET_CODE)
    if (code.includes('qlts.livespo.vn/')) {
        // Extract asset code from URL
        const urlParts = code.split('/');
        const assetCode = urlParts[urlParts.length - 1];
        
        // Redirect to our QR info page
        window.location.href = `/qr/${assetCode}`;
        return;
    }
    
    // Check if it's a direct asset code
    if (code.match(/^[A-Z0-9]+$/)) {
        window.location.href = `/qr/${code}`;
        return;
    }
    
    // Otherwise, try to search by asset code or QR code
    searchAsset(code);
}

function searchAsset(searchTerm) {
    // Check if it's a URL from qlts.livespo.vn
    if (searchTerm.includes('qlts.livespo.vn')) {
        const urlParts = searchTerm.split('/');
        const assetCode = urlParts[urlParts.length - 1];
        if (assetCode) {
            window.location.href = `/qr/${assetCode}`;
            return;
        }
    }
    
    // Direct search by asset code
    window.location.href = `/qr/${encodeURIComponent(searchTerm)}`;
}

function showError(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
    alertDiv.innerHTML = `
        <i class="fas fa-exclamation-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.card-body');
    container.insertBefore(alertDiv, container.firstChild);
}

// Manual search form
document.getElementById('manual-search').addEventListener('submit', function(e) {
    e.preventDefault();
    const searchTerm = document.getElementById('asset-code').value.trim();
    if (searchTerm) {
        searchAsset(searchTerm);
    }
});

// Add to recent scans
function addToRecentScans(assetCode) {
    const recentScansDiv = document.getElementById('recent-scans');
    const scanTime = new Date().toLocaleString('vi-VN');
    
    const scanItem = document.createElement('div');
    scanItem.className = 'border-bottom pb-2 mb-2';
    scanItem.innerHTML = `
        <div class="d-flex justify-content-between">
            <span><strong>${assetCode}</strong></span>
            <small class="text-muted">${scanTime}</small>
        </div>
    `;
    
    if (recentScansDiv.querySelector('p')) {
        recentScansDiv.innerHTML = '';
    }
    
    recentScansDiv.insertBefore(scanItem, recentScansDiv.firstChild);
    
    // Keep only last 5 scans
    const scans = recentScansDiv.querySelectorAll('div.border-bottom');
    if (scans.length > 5) {
        scans[scans.length - 1].remove();
    }
}
</script>
@endsection
@endsection