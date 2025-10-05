@extends('layouts.app')

@section('title', 'Chỉnh sửa tài sản - Livespo Asset Management')
@section('page-title', 'Chỉnh sửa tài sản: ' . $asset->asset_code)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa thông tin tài sản
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('assets.update', $asset) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="asset_code" class="form-label">Mã tài sản <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('asset_code') is-invalid @enderror"
                                       id="asset_code"
                                       name="asset_code"
                                       value="{{ old('asset_code', $asset->asset_code) }}"
                                       required>
                                @error('asset_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-control @error('category_id') is-invalid @enderror"
                                        id="category_id"
                                        name="category_id"
                                        required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                                {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
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

                    <div class="form-group mb-3">
                        <label for="asset_name" class="form-label">Tên tài sản <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('asset_name') is-invalid @enderror"
                               id="asset_name"
                               name="asset_name"
                               value="{{ old('asset_name', $asset->asset_name) }}"
                               required>
                        @error('asset_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="brand" class="form-label">Thương hiệu</label>
                                <input type="text"
                                       class="form-control @error('brand') is-invalid @enderror"
                                       id="brand"
                                       name="brand"
                                       value="{{ old('brand', $asset->brand) }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text"
                                       class="form-control @error('model') is-invalid @enderror"
                                       id="model"
                                       name="model"
                                       value="{{ old('model', $asset->model) }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="serial_number" class="form-label">Số serial</label>
                        <input type="text"
                               class="form-control @error('serial_number') is-invalid @enderror"
                               id="serial_number"
                               name="serial_number"
                               value="{{ old('serial_number', $asset->serial_number) }}">
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="purchase_date" class="form-label">Ngày mua</label>
                                <input type="date"
                                       class="form-control @error('purchase_date') is-invalid @enderror"
                                       id="purchase_date"
                                       name="purchase_date"
                                       value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="purchase_price" class="form-label">Giá mua (VNĐ)</label>
                                <input type="number"
                                       class="form-control @error('purchase_price') is-invalid @enderror"
                                       id="purchase_price"
                                       name="purchase_price"
                                       value="{{ old('purchase_price', $asset->purchase_price) }}"
                                       min="0"
                                       step="1000">
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="warranty_expiry" class="form-label">Hết hạn bảo hành</label>
                                <input type="date"
                                       class="form-control @error('warranty_expiry') is-invalid @enderror"
                                       id="warranty_expiry"
                                       name="warranty_expiry"
                                       value="{{ old('warranty_expiry', $asset->warranty_expiry?->format('Y-m-d')) }}">
                                @error('warranty_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="condition_status" class="form-label">Tình trạng</label>
                                <select class="form-control @error('condition_status') is-invalid @enderror"
                                        id="condition_status"
                                        name="condition_status">
                                    <option value="new" {{ old('condition_status', $asset->condition_status) == 'new' ? 'selected' : '' }}>Mới</option>
                                    <option value="good" {{ old('condition_status', $asset->condition_status) == 'good' ? 'selected' : '' }}>Tốt</option>
                                    <option value="fair" {{ old('condition_status', $asset->condition_status) == 'fair' ? 'selected' : '' }}>Khá</option>
                                    <option value="poor" {{ old('condition_status', $asset->condition_status) == 'poor' ? 'selected' : '' }}>Kém</option>
                                </select>
                                @error('condition_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="location" class="form-label">Vị trí</label>
                                <input type="text"
                                       class="form-control @error('location') is-invalid @enderror"
                                       id="location"
                                       name="location"
                                       value="{{ old('location', $asset->location) }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-control @error('status') is-invalid @enderror"
                                        id="status"
                                        name="status">
                                    <option value="available" {{ old('status', $asset->status) == 'available' ? 'selected' : '' }}>Khả dụng</option>
                                    <option value="assigned" {{ old('status', $asset->status) == 'assigned' ? 'selected' : '' }}>Đã giao</option>
                                    <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                                    <option value="retired" {{ old('status', $asset->status) == 'retired' ? 'selected' : '' }}>Ngừng sử dụng</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lưu ý:</strong> Mọi thay đổi sẽ được ghi lại trong lịch sử tài sản.
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật tài sản
                        </button>
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-qrcode me-2"></i>
                    QR Code hiện tại
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img src="{{config('app.url_link_qr')}}/{{$asset->filename}}" alt="qrcode">
                </div>
                <p class="text-muted">{{ $asset->qr_code }}</p>
                <a href="{{ route('assets.qr', $asset) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-expand me-2"></i>Xem lớn
                </a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>
                    Thông tin bổ sung
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Tạo lúc:</strong></td>
                        <td>{{ $asset->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Cập nhật:</strong></td>
                        <td>{{ $asset->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Lần giao:</strong></td>
                        <td>{{ $asset->assignments->count() }} lần</td>
                    </tr>
                    <tr>
                        <td><strong>Sự cố:</strong></td>
                        <td>{{ $asset->incidentReports->count() }} lần</td>
                    </tr>
                </table>

                <hr>

                <div class="d-grid">
                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-2"></i>Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
