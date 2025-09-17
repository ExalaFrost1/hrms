<?php

namespace App\Services;

use App\Models\EmployeeDailyAttendance;
use App\Models\LeaveRequest;
use App\Models\EmployeeLeaveBalance;
use App\Models\DiscordUserMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscordAttendanceService
{
    // Cache keys
    const CACHE_PREFIX = 'discord_attendance:';
    const BALANCE_CACHE_TTL = 600; // 10 minutes
    const ATTENDANCE_CACHE_TTL = 300; // 5 minutes

    // Default leave allocations (can be moved to config file)
    const DEFAULT_ANNUAL_LEAVE = 25;
    const DEFAULT_SICK_LEAVE = 12;
    const DEFAULT_BEREAVEMENT_LEAVE = 5;

    /**
     * Load employee attendance data (replaces load_employee_data function)
     */
    public function loadEmployeeData($startDate = null, $endDate = null)
    {
        $query = EmployeeDailyAttendance::with('discordUserMapping');

        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        } elseif (!$startDate && !$endDate) {
            // Default to last 30 days
            $query->byDateRange(now()->subDays(30), now());
        }

        $attendanceRecords = $query->get();

        $employees = [];
        foreach ($attendanceRecords as $record) {
            $employeeId = $record->discord_user_id;
            $date = $record->attendance_date->format('Y-m-d');

            if (!isset($employees[$employeeId])) {
                $employees[$employeeId] = [];
            }

            $employees[$employeeId][$date] = [
                'name' => $record->employee_name,
                'display_name' => $record->display_name,
                'status' => $record->status,
                'last_update' => $record->last_update,
                'total_work_time' => $this->parseTimeStr($record->total_work_time),
                'total_break_time' => $this->parseTimeStr($record->total_break_time),
                'screen_time' => $this->parseTimeStr($record->screen_time),
                'check_in_time' => $record->check_in_time,
                'check_out_time' => $record->check_out_time,
                'break_start_time' => $record->break_start_time,
                'screen_share_start' => $record->screen_share_start,
            ];
        }

        return $employees;
    }

    /**
     * Save employee attendance data (replaces save_employee_data function)
     */
    public function saveEmployeeData($employeesData)
    {
        try {
            DB::beginTransaction();

            foreach ($employeesData as $employeeId => $dates) {
                foreach ($dates as $date => $data) {
                    EmployeeDailyAttendance::updateOrCreate(
                        [
                            'discord_user_id' => (string) $employeeId,
                            'attendance_date' => $date,
                        ],
                        [
                            'employee_name' => $data['name'],
                            'display_name' => $data['display_name'] ?? $data['name'],
                            'status' => $data['status'],
                            'last_update' => $data['last_update'] ?? now(),
                            'total_work_time' => $this->formatTimedelta($data['total_work_time']),
                            'total_break_time' => $this->formatTimedelta($data['total_break_time']),
                            'screen_time' => $this->formatTimedelta($data['screen_time']),
                            'check_in_time' => $data['check_in_time'],
                            'check_out_time' => $data['check_out_time'],
                            'break_start_time' => $data['break_start_time'],
                            'screen_share_start' => $data['screen_share_start'],
                        ]
                    );

                    // Clear cache for this employee
                    $this->clearAttendanceCache($employeeId);
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving employee data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save leave request (replaces save_leave_request function)
     */
    public function saveLeaveRequest($requestData)
    {
        try {
            $leaveRequest = LeaveRequest::create([
                'request_id' => $requestData['request_id'] ?? null,
                'employee_name' => $requestData['employee_name'],
                'full_name' => $requestData['full_name'],
                'email' => $requestData['email'] ?? '',
                'department' => $requestData['department'] ?? '',
                'leave_type' => $requestData['leave_type'],
                'half_day_period' => $requestData['half_day_period'] ?? null,
                'reason' => $requestData['reason'],
                'start_date' => $requestData['start_date'],
                'end_date' => $requestData['end_date'],
                'description' => $requestData['description'] ?? '',
                'status' => $requestData['status'] ?? 'pending',
                'approver_username' => $requestData['approver_username'] ?? '',
                'thread_id' => $requestData['thread_id'] ?? '',
                'rejection_reason' => $requestData['rejection_reason'] ?? '',
                'attachment_filename' => $requestData['attachment_filename'] ?? '',
                'attachment_path' => $requestData['attachment_path'] ?? '',
            ]);

            // Clear balance cache for this employee
            $this->clearBalanceCache($requestData['employee_name']);

            return true;

        } catch (\Exception $e) {
            Log::error('Error saving leave request: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update leave request status (replaces update_leave_request function)
     */
    public function updateLeaveRequest($requestId, $action, $approverUsername, $rejectionReason = '')
    {
        try {
            DB::beginTransaction();

            $leaveRequest = LeaveRequest::where('request_id', $requestId)->first();

            if (!$leaveRequest) {
                Log::warning("Leave request not found: {$requestId}");
                return null;
            }

            $oldStatus = $leaveRequest->status;

            $leaveRequest->update([
                'status' => $action,
                'approver_username' => $approverUsername,
                'rejection_reason' => $rejectionReason,
            ]);

            // Update leave balance if approved or if reversing approval
            if ($action === 'approved' && $oldStatus !== 'approved') {
                $this->updateEmployeeLeaveBalance(
                    $leaveRequest->employee_name,
                    $leaveRequest->leave_category,
                    $leaveRequest->calculated_days
                );
            } elseif ($action === 'rejected' && $oldStatus === 'approved') {
                // Reverse the leave balance if request was previously approved
                $this->updateEmployeeLeaveBalance(
                    $leaveRequest->employee_name,
                    $leaveRequest->leave_category,
                    -$leaveRequest->calculated_days
                );
            }

            // Clear balance cache
            $this->clearBalanceCache($leaveRequest->employee_name);

            DB::commit();

            return $leaveRequest->toArray();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating leave request: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get leave requests with filters (replaces get_leave_requests function)
     */
    public function getLeaveRequests($employeeName = null, $startDate = null, $endDate = null, $status = null)
    {
        $query = LeaveRequest::query();

        if ($employeeName) {
            $query->byEmployee($employeeName);
        }

        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * Get employee leave balance (replaces get_real_employee_leave_balance function)
     */
    public function getEmployeeLeaveBalance($discordUsername)
    {
        $cacheKey = self::CACHE_PREFIX . 'balance:' . $discordUsername;

        return Cache::remember($cacheKey, self::BALANCE_CACHE_TTL, function () use ($discordUsername) {
            // First try to find by Discord username
            $mapping = DiscordUserMapping::where('discord_username', $discordUsername)
                ->where('is_active', true)
                ->first();

            if (!$mapping) {
                // Try to find by Discord user ID if username lookup fails
                $mapping = DiscordUserMapping::where('discord_user_id', $discordUsername)
                    ->where('is_active', true)
                    ->first();
            }

            if (!$mapping) {
                Log::warning("Discord user mapping not found for: {$discordUsername}");
                return null;
            }

            $balance = EmployeeLeaveBalance::currentYear()
                ->byUser($mapping->discord_user_id)
                ->first();

            if (!$balance) {
                // Create default balance if not exists
                $balance = EmployeeLeaveBalance::getOrCreateCurrentBalance(
                    $mapping->discord_user_id,
                    $mapping->discord_username,
                    'full_time'
                );
            }

            return $balance->getBalanceArray();
        });
    }

    /**
     * Update employee leave balance (replaces update_employee_leave_balance function)
     */
    public function updateEmployeeLeaveBalance($discordUsername, $leaveType, $daysUsed)
    {
        try {
            $mapping = DiscordUserMapping::where('discord_username', $discordUsername)
                ->where('is_active', true)
                ->first();

            if (!$mapping) {
                Log::error("Cannot update leave balance - Discord user not found: {$discordUsername}");
                return false;
            }

            $balance = EmployeeLeaveBalance::currentYear()
                ->byUser($mapping->discord_user_id)
                ->first();

            if (!$balance) {
                $balance = EmployeeLeaveBalance::getOrCreateCurrentBalance(
                    $mapping->discord_user_id,
                    $mapping->discord_username
                );
            }

            $balance->updateLeaveUsage($leaveType, $daysUsed);

            // Clear cache
            $this->clearBalanceCache($discordUsername);

            Log::info("Updated {$leaveType} leave balance for {$discordUsername}: {$daysUsed} days");
            return true;

        } catch (\Exception $e) {
            Log::error('Error updating employee leave balance: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format leave balance message (replaces format_real_leave_balance_message function)
     */
    public function formatLeaveBalanceMessage($leaveBalance)
    {
        if (!$leaveBalance) {
            return "[ERROR] Could not retrieve leave balance from system.";
        }

        $message = "**Leave Balance for {$leaveBalance['employee_name']}:**\n\n";

        // Annual Leave
        $annual = $leaveBalance['annual'];
        $message .= "**Annual Leave:** {$annual['used']} used, {$annual['remaining']} remaining (of {$annual['total']} total)\n";

        // Sick Leave
        $sick = $leaveBalance['sick'];
        $message .= "**Sick Leave:** {$sick['used']} used, {$sick['remaining']} remaining (of {$sick['total']} total)\n";

        // Bereavement Leave
        $bereavement = $leaveBalance['bereavement'];
        $message .= "**Bereavement Leave:** {$bereavement['used']} used, {$bereavement['remaining']} remaining (of {$bereavement['total']} total)\n";

        // Employment Type
        $message .= "**Employment Type:** {$leaveBalance['employment_type']}";

        return $message;
    }

    /**
     * Calculate leave days (replaces calculate_leave_days function)
     */
    public function calculateLeaveDays($startDate, $endDate, $leaveType, $halfDayPeriod = null)
    {
        try {
            if (!$startDate || !$endDate) {
                return 0;
            }

            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $days = $start->diffInDays($end) + 1; // Include both start and end dates

            // Adjust for half days
            if ($leaveType && str_contains(strtolower($leaveType), 'half')) {
                return $days * 0.5;
            }

            return $days;

        } catch (\Exception $e) {
            Log::error('Error calculating leave days: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Categorize leave type (replaces categorize_leave_type function)
     */
    public function categorizeLeaveType($reason)
    {
        if (!$reason) {
            return 'annual';
        }

        $reasonLower = strtolower($reason);

        // Check for sick leave
        $sickKeywords = ['sick', 'illness', 'medical', 'doctor', 'hospital', 'fever', 'health', 'appointment'];
        foreach ($sickKeywords as $keyword) {
            if (str_contains($reasonLower, $keyword)) {
                return 'sick';
            }
        }

        // Check for bereavement leave
        $bereavementKeywords = ['bereavement', 'death', 'funeral'];
        foreach ($bereavementKeywords as $keyword) {
            if (str_contains($reasonLower, $keyword)) {
                return 'bereavement';
            }
        }

        // Default to annual leave
        return 'annual';
    }

    /**
     * Check if employee has sufficient leave balance
     */
    public function hasSufficientBalance($discordUsername, $leaveType, $days)
    {
        $balance = $this->getEmployeeLeaveBalance($discordUsername);

        if (!$balance) {
            return false;
        }

        switch ($leaveType) {
            case 'annual':
                return $balance['annual']['remaining'] >= $days;
            case 'sick':
                return $balance['sick']['remaining'] >= $days;
            case 'bereavement':
                return $balance['bereavement']['remaining'] >= $days;
            default:
                return false;
        }
    }

    /**
     * Get or create Discord user mapping
     */
    public function getOrCreateDiscordMapping($discordUserId, $discordUsername, $discordDisplayName = null, $employeeId = null)
    {
        return DiscordUserMapping::getOrCreateMapping(
            $discordUserId,
            $discordUsername,
            $discordDisplayName,
            $employeeId
        );
    }

    /**
     * Get today's attendance record
     */
    public function getTodayAttendance($discordUserId, $employeeName, $displayName = null)
    {
        return EmployeeDailyAttendance::getOrCreateTodayRecord($discordUserId, $employeeName, $displayName);
    }

    /**
     * Update attendance status
     */
    public function updateAttendanceStatus($discordUserId, $status, $timestamp = null)
    {
        $attendance = $this->getTodayAttendance($discordUserId, '', '');

        $attendance->status = $status;
        $attendance->last_update = $timestamp ?? now();

        // Handle specific status updates
        switch ($status) {
            case 'checked_in':
                if (!$attendance->check_in_time) {
                    $attendance->check_in_time = $timestamp ?? now();
                }
                break;

            case 'checked_out':
                $attendance->check_out_time = $timestamp ?? now();
                $attendance->updateWorkTime();
                break;

            case 'on_break':
                $attendance->break_start_time = $timestamp ?? now();
                break;

            case 'screen_sharing':
                $attendance->screen_share_start = $timestamp ?? now();
                break;
        }

        $attendance->save();

        // Clear cache
        $this->clearAttendanceCache($discordUserId);

        return $attendance;
    }

    /**
     * Get employee statistics
     */
    public function getEmployeeStats($discordUserId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now();

        $attendance = EmployeeDailyAttendance::byUser($discordUserId)
            ->byDateRange($startDate, $endDate)
            ->get();

        $totalWorkMinutes = 0;
        $totalBreakMinutes = 0;
        $totalScreenMinutes = 0;
        $daysWorked = $attendance->count();

        foreach ($attendance as $record) {
            $totalWorkMinutes += $this->timeStringToMinutes($record->total_work_time);
            $totalBreakMinutes += $this->timeStringToMinutes($record->total_break_time);
            $totalScreenMinutes += $this->timeStringToMinutes($record->screen_time);
        }

        return [
            'days_worked' => $daysWorked,
            'total_work_hours' => round($totalWorkMinutes / 60, 2),
            'total_break_hours' => round($totalBreakMinutes / 60, 2),
            'total_screen_hours' => round($totalScreenMinutes / 60, 2),
            'average_work_hours_per_day' => $daysWorked > 0 ? round(($totalWorkMinutes / 60) / $daysWorked, 2) : 0,
        ];
    }

    // Helper functions

    private function parseTimeStr($timeStr)
    {
        if (!$timeStr) return 0;

        $parts = explode(':', $timeStr);
        if (count($parts) >= 2) {
            return (int)$parts[0] * 60 + (int)$parts[1]; // Convert to minutes
        }

        return 0;
    }

    private function formatTimedelta($minutes)
    {
        if (!$minutes) return '00:00:00';

        $hours = intval($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d:00', $hours, $mins);
    }

    private function timeStringToMinutes($timeStr)
    {
        if (!$timeStr || $timeStr === '00:00:00') return 0;

        $parts = explode(':', $timeStr);
        if (count($parts) >= 2) {
            return (int)$parts[0] * 60 + (int)$parts[1];
        }

        return 0;
    }

    private function clearAttendanceCache($discordUserId)
    {
        Cache::forget(self::CACHE_PREFIX . 'attendance:' . $discordUserId);
    }

    private function clearBalanceCache($discordUsername)
    {
        Cache::forget(self::CACHE_PREFIX . 'balance:' . $discordUsername);
    }

    /**
     * Initialize employee leave balances for new year
     */
    public function initializeYearlyBalances($year = null)
    {
        $year = $year ?? now()->year;

        $mappings = DiscordUserMapping::active()->get();

        foreach ($mappings as $mapping) {
            EmployeeLeaveBalance::firstOrCreate(
                [
                    'discord_user_id' => $mapping->discord_user_id,
                    'year' => $year,
                ],
                [
                    'employee_name' => $mapping->discord_username,
                    'employment_type' => 'full_time',
                    'date_of_joining' => now()->startOfYear(),
                    'annual_entitled' => self::DEFAULT_ANNUAL_LEAVE,
                    'sick_entitled' => self::DEFAULT_SICK_LEAVE,
                    'bereavement_entitled' => self::DEFAULT_BEREAVEMENT_LEAVE,
                ]
            );
        }
    }

    /**
     * Generate leave report for admin
     */
    public function generateLeaveReport($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->startOfYear();
        $endDate = $endDate ?? now();

        $leaveRequests = LeaveRequest::byDateRange($startDate, $endDate)
            ->with(['discordUserMapping.employee'])
            ->get()
            ->groupBy('employee_name');

        $report = [];

        foreach ($leaveRequests as $employeeName => $requests) {
            $totalDays = $requests->where('status', 'approved')->sum('calculated_days');
            $pendingDays = $requests->where('status', 'pending')->sum('calculated_days');

            $report[$employeeName] = [
                'total_requests' => $requests->count(),
                'approved_days' => $totalDays,
                'pending_days' => $pendingDays,
                'rejected_requests' => $requests->where('status', 'rejected')->count(),
                'by_category' => [
                    'annual' => $requests->where('leave_category', 'annual')->where('status', 'approved')->sum('calculated_days'),
                    'sick' => $requests->where('leave_category', 'sick')->where('status', 'approved')->sum('calculated_days'),
                    'bereavement' => $requests->where('leave_category', 'bereavement')->where('status', 'approved')->sum('calculated_days'),
                ]
            ];
        }

        return $report;
    }
}
