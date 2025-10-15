<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetAssignment::with(['asset.category', 'employee.department', 'assignedBy']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                  ->orWhere('asset_name', 'like', "%{$search}%");
            })->orWhereHas('employee', function($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $assignments = $query->orderBy('assigned_date', 'desc')->paginate(15);

        return view('assignments.index', compact('assignments'));
    }

    public function create(Request $request)
    {
        $availableAssets = Asset::where('status', 'available')->with('category')->get();
        $employees = Employee::where('status', 'active')->with('department')->get();

        // Pre-select asset if asset_id is provided
        $selectedAssetId = $request->get('asset_id');

        return view('assignments.create', compact('availableAssets', 'employees', 'selectedAssetId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after:assigned_date',
            'assignment_notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            // Check if asset is still available
            $asset = Asset::findOrFail($request->asset_id);
            if ($asset->status !== 'available') {
                throw new \Exception('Tài sản không khả dụng để cấp phát.');
            }

            // Create assignment
            $assignment = AssetAssignment::create([
                'asset_id' => $request->asset_id,
                'employee_id' => $request->employee_id,
                'assigned_by' => Auth::id(),
                'assigned_date' => $request->assigned_date,
                'expected_return_date' => $request->expected_return_date,
                'assignment_notes' => $request->assignment_notes,
                'status' => 'active'
            ]);

            // Update asset status
            $asset->update(['status' => 'assigned']);

            // Log history
            AssetHistory::create([
                'asset_id' => $request->asset_id,
                'action_type' => 'assigned',
                'performed_by' => Auth::id(),
                'old_value' => json_encode(['status' => 'available']),
                'new_value' => json_encode([
                    'status' => 'assigned',
                    'employee_id' => $request->employee_id,
                    'assignment_id' => $assignment->id
                ]),
                'notes' => 'Tài sản được cấp phát cho nhân viên'
            ]);
        });

        return redirect()->route('assignments.index')
            ->with('success', 'Tài sản đã được cấp phát thành công.');
    }

    public function show(AssetAssignment $assignment)
    {
        $assignment->load(['asset.category', 'employee.department', 'assignedBy']);
        return view('assignments.show', compact('assignment'));
    }

    public function edit(AssetAssignment $assignment)
    {
        if ($assignment->status !== 'active') {
            return redirect()->route('assignments.index')
                ->with('error', 'Chỉ có thể chỉnh sửa cấp phát đang hoạt động.');
        }

        $employees = Employee::where('status', 'active')->with('department')->get();
        return view('assignments.edit', compact('assignment', 'employees'));
    }

    public function update(Request $request, AssetAssignment $assignment)
    {
        if ($assignment->status !== 'active') {
            return redirect()->route('assignments.index')
                ->with('error', 'Chỉ có thể cập nhật cấp phát đang hoạt động.');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'expected_return_date' => 'nullable|date|after:assigned_date',
            'assignment_notes' => 'nullable|string'
        ]);

        $oldEmployeeId = $assignment->employee_id;

        $assignment->update([
            'employee_id' => $request->employee_id,
            'expected_return_date' => $request->expected_return_date,
            'assignment_notes' => $request->assignment_notes
        ]);

        // Log history if employee changed
        if ($oldEmployeeId != $request->employee_id) {
            AssetHistory::create([
                'asset_id' => $assignment->asset_id,
                'action_type' => 'assigned',
                'performed_by' => Auth::id(),
                'old_value' => json_encode(['employee_id' => $oldEmployeeId]),
                'new_value' => json_encode(['employee_id' => $request->employee_id]),
                'notes' => 'Chuyển cấp phát tài sản cho nhân viên khác'
            ]);
        }

        return redirect()->route('assignments.index')
            ->with('success', 'Thông tin cấp phát đã được cập nhật.');
    }

    public function return(Request $request, AssetAssignment $assignment)
    {
        if ($assignment->status !== 'active') {
            return redirect()->route('assignments.index')
                ->with('error', 'Cấp phát này đã được thu hồi.');
        }

        $request->validate([
            'actual_return_date' => 'required|date',
            'return_condition' => 'required|in:good,fair,poor,damaged,lost',
            'asset_status' => 'required|in:available,maintenance,retired',
            'return_notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $assignment) {
            // Update assignment
            $assignment->update([
                'actual_return_date' => $request->actual_return_date,
                'return_condition' => $request->return_condition,
                'return_notes' => $request->return_notes,
                'status' => 'returned'
            ]);

            // Update asset status based on user selection
            $assignment->asset->update([
                'status' => $request->asset_status,
                'condition_status' => $request->return_condition
            ]);

            // Log history
            AssetHistory::create([
                'asset_id' => $assignment->asset_id,
                'action_type' => 'returned',
                'performed_by' => Auth::id(),
                'old_value' => json_encode(['status' => 'assigned']),
                'new_value' => json_encode([
                    'status' => $request->asset_status,
                    'condition' => $request->return_condition
                ]),
                'notes' => 'Tài sản được thu hồi từ nhân viên'
            ]);
        });

        return redirect()->route('assignments.index')
            ->with('success', 'Tài sản đã được thu hồi thành công.');
    }

    public function showReturnForm(AssetAssignment $assignment)
    {
        if ($assignment->status !== 'active') {
            return redirect()->route('assignments.index')
                ->with('error', 'Cấp phát này đã được thu hồi.');
        }

        $assignment->load(['asset.category', 'employee.department']);
        return view('assignments.return', compact('assignment'));
    }

    public function getExpiringAssignments()
    {
        $expiringAssignments = AssetAssignment::with(['asset', 'employee'])
            ->where('status', 'active')
            ->whereNotNull('expected_return_date')
            ->where('expected_return_date', '<=', now()->addDays(30))
            ->orderBy('expected_return_date')
            ->get();

        return response()->json($expiringAssignments);
    }
}
