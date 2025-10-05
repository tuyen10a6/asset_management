<?php

namespace App\Http\Controllers;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetHistory;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['category']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('asset_name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->get('category') != '') {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->has('status') && $request->get('status') != '') {
            $query->where('status', $request->get('status'));
        }

        $assets = $query->paginate(15);
        $categories = AssetCategory::where('is_active', true)->get();

        return view('assets.index', compact('assets', 'categories'));
    }

    public function create()
    {
        $categories = AssetCategory::where('is_active', true)->get();
        return view('assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|unique:assets',
            'asset_name' => 'required|max:100',
            'category_id' => 'required|exists:asset_categories,id',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
        ]);

        $data = $request->all();

        $data['qr_code'] = 'https://qlts.livespo.vn/' . $data['asset_code'];


        $fileName = $this->__generateQrcode($data['asset_code'], $data['qr_code']);

        $data['filename'] = $fileName;

        $asset = Asset::query()->create($data);

        // Log history
        AssetHistory::create([
            'asset_id' => $asset->id,
            'action_type' => 'created',
            'action_date' => now(),
            'performed_by' => Auth::user()->employee_id,
            'notes' => 'Tài sản được tạo mới'
        ]);

        return redirect()->route('assets.index')->with('success', 'Tài sản đã được tạo thành công!');
    }

    private function __generateQrcode( $serialCode, $qrcodeContent): string
    {
        $qrCode = \Endroid\QrCode\QrCode::create($qrcodeContent)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $logo = Logo::create(public_path('statics/images/logo_qrcode.png'))
            ->setResizeToWidth(50)
            ->setPunchoutBackground(true);

        $writer = new PngWriter();
        $filename = $serialCode . '.png';

        $outputQrCode = public_path('statics/images/qrcode/' . $filename);
        $result = $writer->write($qrCode, $logo);
        $result->saveToFile($outputQrCode);

        return $filename;
    }
    public function show(Asset $asset)
    {
        $asset->load(['category', 'assignments.employee', 'histories.performedBy', 'incidentReports']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::where('is_active', true)->get();
        return view('assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_code' => 'required|unique:assets,asset_code,' . $asset->id,
            'asset_name' => 'required|max:100',
            'category_id' => 'required|exists:asset_categories,id',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty_expiry' => 'nullable|date',
        ]);

        $oldData = $asset->toArray();
        $asset->update($request->all());

        // Log history
        AssetHistory::create([
            'asset_id' => $asset->id,
            'action_type' => 'updated',
            'action_date' => now(),
            'performed_by' => Auth::user()->employee_id,
            'old_value' => json_encode($oldData),
            'new_value' => json_encode($asset->fresh()->toArray()),
            'notes' => 'Thông tin tài sản được cập nhật'
        ]);

        return redirect()->route('assets.index')->with('success', 'Tài sản đã được cập nhật thành công!');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->assignments()->where('status', 'active')->exists()) {
            return redirect()->route('assets.index')->with('error', 'Không thể xóa tài sản đang được giao!');
        }

        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Tài sản đã được xóa thành công!');
    }

    public function showQR(Asset $asset)
    {
        // Generate QR code with the required URL format
        $qrUrl = 'https://qlts.livespo.vn/' . strtolower($asset->asset_code);

        $qrCode = QrCode::size(300)
            ->format('svg')
            ->generate($qrUrl);

        return view('assets.qr', compact('asset', 'qrCode'));
    }

    public function showAssetByQR($asset_code)
    {
        $asset = Asset::where('asset_code', $asset_code)
            ->with(['category', 'currentAssignment.employee.department'])
            ->firstOrFail();

        return view('assets.qr-info', compact('asset'));
    }


}
