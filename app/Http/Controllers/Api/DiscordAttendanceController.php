<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiscordAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DiscordAttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(DiscordAttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Load employee attendance data
     * Replaces: load_employee_data()
     */
    public function loadEmployeeData(Request $request): JsonResponse
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
            $employees = $this->attendanceService->loadEmployeeData(
                $request->start_date,
                $request->end_date
            );

            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading employee data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save employee attendance data
     * Replaces: save_employee_data() / async_save_employee_data()
     */
    public function saveEmployeeData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employees' => 'required|array',
            'employees.*.*.name' => 'required|string',
            'employees.*.*.display_name' => 'nullable|string',
            'employees.*.*.status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $result = $this->attendanceService->saveEmployeeData($request->employees);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Employee data saved successfully' : 'Failed to save employee data'
            ], $result ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving employee data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update attendance status for a specific user
     * New endpoint for real-time attendance tracking
     */
    public function updateAttendanceStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discord_user_id' => 'required|string',
            'employee_name' => 'required|string',
            'display_name' => 'nullable|string',
            'status' => 'required|in:checked_in,checked_out,on_break,screen_sharing,offline',
            'timestamp' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Ensure Discord mapping exists
            $this->attendanceService->getOrCreateDiscordMapping(
                $request->discord_user_id,
                $request->employee_name,
                $request->display_name
            );

            $attendance = $this->attendanceService->updateAttendanceStatus(
                $request->discord_user_id,
                $request->status,
                $request->timestamp ? Carbon::parse($request->timestamp) : null
            );

            return response()->json([
                'success' => true,
                'data' => $attendance,
                'message' => 'Attendance status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating attendance status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's attendance for a user
     */
    public function getTodayAttendance(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discord_user_id' => 'required|string',
            'employee_name' => 'required|string',
            'display_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $attendance = $this->attendanceService->getTodayAttendance(
                $request->discord_user_id,
                $request->employee_name,
                $request->display_name
            );

            return response()->json([
                'success' => true,
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee statistics
     */
    public function getEmployeeStats(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discord_user_id' => 'required|string',
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
            $stats = $this->attendanceService->getEmployeeStats(
                $request->discord_user_id,
                $request->start_date ? Carbon::parse($request->start_date) : null,
                $request->end_date ? Carbon::parse($request->end_date) : null
            );

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching employee stats: ' . $e->getMessage()
            ], 500);
        }
    }
}
