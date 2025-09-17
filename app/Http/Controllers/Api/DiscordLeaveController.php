<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiscordAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DiscordLeaveController extends Controller
{
    protected $attendanceService;

    public function __construct(DiscordAttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Save leave request
     * Replaces: save_leave_request() / async_save_leave_request()
     */
    public function saveLeaveRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'nullable|string|unique:leave_requests,request_id',
            'employee_name' => 'required|string',
            'full_name' => 'required|string',
            'email' => 'nullable|email',
            'department' => 'nullable|string',
            'leave_type' => 'required|in:Full Day,Half Day,Emergency',
            'half_day_period' => 'nullable|in:First Half,Second Half',
            'reason' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'thread_id' => 'nullable|string',
            'attachment_filename' => 'nullable|string',
            'attachment_path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $result = $this->attendanceService->saveLeaveRequest($request->all());

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Leave request saved successfully' : 'Failed to save leave request'
            ], $result ? 201 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update leave request status
     * Replaces: update_leave_request() / async_update_leave_request()
     */
    public function updateLeaveRequest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|string',
            'action' => 'required|in:approved,rejected,pending',
            'approver_username' => 'required|string',
            'rejection_reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $updatedRequest = $this->attendanceService->updateLeaveRequest(
                $request->request_id,
                $request->action,
                $request->approver_username,
                $request->rejection_reason ?? ''
            );

            if ($updatedRequest) {
                return response()->json([
                    'success' => true,
                    'data' => $updatedRequest,
                    'message' => 'Leave request updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Leave request not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get leave requests with filters
     * Replaces: get_leave_requests()
     */
    public function getLeaveRequests(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_name' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $leaveRequests = $this->attendanceService->getLeaveRequests(
                $request->employee_name,
                $request->start_date,
                $request->end_date,
                $request->status
            );

            return response()->json([
                'success' => true,
                'data' => $leaveRequests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching leave requests: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee leave balance
     * Replaces: get_real_employee_leave_balance() / async_get_real_employee_leave_balance()
     */
    public function getEmployeeLeaveBalance(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discord_username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $balance = $this->attendanceService->getEmployeeLeaveBalance($request->discord_username);

            if ($balance) {
                return response()->json([
                    'success' => true,
                    'data' => $balance
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee leave balance not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching leave balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get formatted leave balance message
     * Replaces: format_real_leave_balance_message()
     */
    public function getFormattedLeaveBalance(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discord_username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $balance = $this->attendanceService->getEmployeeLeaveBalance($request->discord_username);
            $message = $this->attendanceService->formatLeaveBalanceMessage($balance);

            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => $balance,
                    'formatted_message' => $message
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error formatting leave balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if employee has sufficient balance
     */
    public function checkSufficientBalance(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discord_username' => 'required|string',
            'leave_type' => 'required|in:annual,sick,bereavement',
            'days' => 'required|numeric|min:0.5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $hasSufficientBalance = $this->attendanceService->hasSufficientBalance(
                $request->discord_username,
                $request->leave_type,
                $request->days
            );

            $balance = $this->attendanceService->getEmployeeLeaveBalance($request->discord_username);

            return response()->json([
                'success' => true,
                'data' => [
                    'has_sufficient_balance' => $hasSufficientBalance,
                    'current_balance' => $balance[$request->leave_type] ?? null,
                    'requested_days' => $request->days
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate leave days
     * Replaces: calculate_leave_days()
     */
    public function calculateLeaveDays(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|string',
            'half_day_period' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $days = $this->attendanceService->calculateLeaveDays(
                $request->start_date,
                $request->end_date,
                $request->leave_type,
                $request->half_day_period
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'calculated_days' => $days,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'leave_type' => $request->leave_type
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating leave days: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Categorize leave type based on reason
     * Replaces: categorize_leave_type()
     */
    public function categorizeLeaveType(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $category = $this->attendanceService->categorizeLeaveType($request->reason);

            return response()->json([
                'success' => true,
                'data' => [
                    'reason' => $request->reason,
                    'category' => $category
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error categorizing leave type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate leave report
     */
    public function generateLeaveReport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $report = $this->attendanceService->generateLeaveReport(
                $request->start_date ? Carbon::parse($request->start_date) : null,
                $request->end_date ? Carbon::parse($request->end_date) : null
            );

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating leave report: ' . $e->getMessage()
            ], 500);
        }
    }
}
