<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveRequestFormRequest;
use App\Models\LeaveRequest;
use App\Models\ManagerDelegation;
use App\Notifications\LeaveRequestCancelled;
use App\Notifications\LeaveRequestSubmitted;
use App\Services\ConflictDetectionService;
use App\Services\LeaveBalanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveRequestController extends Controller
{
    protected LeaveBalanceService $leaveBalanceService;
    protected ConflictDetectionService $conflictDetectionService;

    public function __construct(
        LeaveBalanceService $leaveBalanceService,
        ConflictDetectionService $conflictDetectionService
    ) {
        $this->leaveBalanceService = $leaveBalanceService;
        $this->conflictDetectionService = $conflictDetectionService;
    }

    /**
     * Display a listing of the user's leave requests.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = LeaveRequest::with(['manager'])
            ->where('user_id', $user->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $leaveRequests = $query->orderByDesc('submitted_at')
            ->paginate(15)
            ->withQueryString();

        // Get leave balances for the current year
        $leaveBalances = $this->leaveBalanceService->getUserBalances($user->id);

        return view('leave-requests.index', [
            'leaveRequests' => $leaveRequests,
            'leaveBalances' => $leaveBalances,
        ]);
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        $leaveTypes = [
            'paid_time_off' => 'Paid Time Off',
            'unpaid_leave' => 'Unpaid Leave',
            'sick_leave' => 'Sick Leave',
            'vacation' => 'Vacation',
        ];

        // Get leave balances for the current year
        $leaveBalances = $this->leaveBalanceService->getUserBalances($user->id);

        return view('leave-requests.create', [
            'leaveTypes' => $leaveTypes,
            'leaveBalances' => $leaveBalances,
        ]);
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(LeaveRequestFormRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Check if user has a manager assigned
        if (! $user->hasManager()) {
            return back()
                ->withErrors(['manager' => 'You do not have a manager assigned. Please contact HR.'])
                ->withInput();
        }

        // Calculate working days (excluding weekends and holidays)
        $totalDays = $this->leaveBalanceService->calculateWorkingDays(
            $request->start_date,
            $request->end_date,
            excludeHolidays: true,
            region: $user->department
        );

        // Check if user has sufficient balance
        if (! $this->leaveBalanceService->validateBalanceSufficiency(
            $user->id,
            $request->leave_type,
            $totalDays
        )) {
            return back()
                ->withErrors(['balance' => 'Insufficient leave balance for this request.'])
                ->withInput();
        }

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        // Determine the approving manager (check for active delegations)
        $approvingManagerId = $user->manager_id;
        $activeDelegate = ManagerDelegation::getActiveDelegate($user->manager_id, $request->start_date);
        if ($activeDelegate) {
            $approvingManagerId = $activeDelegate->id;
        }

        // Create a temporary leave request to check for conflicts
        $tempLeaveRequest = new LeaveRequest([
            'user_id' => $user->id,
            'manager_id' => $approvingManagerId,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'status' => 'pending',
        ]);

        // Check for conflicts with existing approved/pending leaves
        $conflicts = $this->conflictDetectionService->checkConflicts($tempLeaveRequest, $approvingManagerId);

        // Filter for team overlap conflicts (warn about pending/approved leaves)
        $teamConflicts = collect($conflicts)->filter(function ($conflict) {
            return in_array($conflict['type'], ['overlap', 'availability']);
        });

        if ($teamConflicts->isNotEmpty()) {
            $conflictMessages = $teamConflicts->map(function ($conflict) {
                $message = $conflict['message'];
                if (isset($conflict['details']) && is_array($conflict['details']) && isset($conflict['details'][0]['employee'])) {
                    $employees = collect($conflict['details'])->pluck('employee')->join(', ');
                    $message .= ": {$employees}";
                }
                return $message;
            })->join(' | ');

            return back()
                ->withErrors(['conflict' => "⚠️ Team Conflict Detected: {$conflictMessages}. Your request can still be submitted, but your manager will need to review team availability."])
                ->withInput();
        }

        // Create the leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'manager_id' => $approvingManagerId,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'status' => 'pending',
            'employee_notes' => $request->employee_notes,
            'attachment_path' => $attachmentPath,
            'submitted_at' => now(),
        ]);

        // Reserve the balance (move from available to pending)
        try {
            $this->leaveBalanceService->reserveBalance($leaveRequest->id);
        } catch (\Exception $e) {
            // If balance reservation fails, delete the request and show error
            $leaveRequest->delete();

            return back()
                ->withErrors(['balance' => 'Failed to reserve balance: '.$e->getMessage()])
                ->withInput();
        }

        // Record history
        $leaveRequest->recordHistory('submitted', $user->id, 'Leave request submitted');

        // Send notification to manager
        $leaveRequest->manager->notify(new LeaveRequestSubmitted($leaveRequest));

        return redirect()
            ->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Your leave request has been submitted successfully!');
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest): View
    {
        $leaveRequest->load(['user', 'manager', 'history.performedBy']);

        return view('leave-requests.show', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    /**
     * Cancel the specified leave request.
     */
    public function cancel(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        // Check if user owns this request
        if ($leaveRequest->user_id !== $request->user()->id) {
            abort(403, 'You can only cancel your own requests.');
        }

        // Check if request can be cancelled
        if (! $leaveRequest->canBeCancelled()) {
            return back()->withErrors(['status' => 'This request cannot be cancelled.']);
        }

        $leaveRequest->update([
            'status' => 'cancelled',
        ]);

        // Restore balance (move from pending/used back to available)
        try {
            $this->leaveBalanceService->restoreToBalance($leaveRequest->id);
        } catch (\Exception $e) {
            // Log the error but don't fail the cancellation
            logger()->error('Failed to restore balance on cancellation', [
                'leave_request_id' => $leaveRequest->id,
                'error' => $e->getMessage(),
            ]);
        }

        $leaveRequest->recordHistory(
            'cancelled',
            $request->user()->id,
            'Cancelled by employee'
        );

        // Send notification to manager
        $leaveRequest->manager->notify(new LeaveRequestCancelled($leaveRequest));

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'Your leave request has been cancelled.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented for MVP
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Not implemented for MVP
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not implemented - use cancel instead
        abort(404);
    }

    /**
     * Download the attachment for a leave request.
     */
    public function downloadAttachment(LeaveRequest $leaveRequest): StreamedResponse
    {
        // Check authorization - user must own the request or be the manager
        if (auth()->id() !== $leaveRequest->user_id &&
            auth()->id() !== $leaveRequest->manager_id &&
            ! auth()->user()->isHRAdmin()) {
            abort(403, 'Unauthorized to download this attachment.');
        }

        // Check if attachment exists
        if (! $leaveRequest->hasAttachment()) {
            abort(404, 'No attachment found for this leave request.');
        }

        // Check if file exists in storage
        if (! Storage::disk('public')->exists($leaveRequest->attachment_path)) {
            abort(404, 'Attachment file not found.');
        }

        // Get original filename from the path
        $filename = basename($leaveRequest->attachment_path);

        // Download the file
        return Storage::disk('public')->download($leaveRequest->attachment_path, $filename);
    }
}
