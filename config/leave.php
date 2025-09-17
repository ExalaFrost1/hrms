<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Leave Allocations
    |--------------------------------------------------------------------------
    | These are the default leave day allocations for employees.
    | Values are in days and can be decimal for half-day precision.
    */

    'default_allocations' => [
        'annual' => env('DEFAULT_ANNUAL_LEAVE', 25),
        'sick' => env('DEFAULT_SICK_LEAVE', 12),
        'bereavement' => env('DEFAULT_BEREAVEMENT_LEAVE', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Leave Categories and Keywords
    |--------------------------------------------------------------------------
    | These keywords are used to automatically categorize leave requests
    | based on the reason provided.
    */

    'categories' => [
        'sick' => [
            'keywords' => ['sick', 'illness', 'medical', 'doctor', 'hospital', 'fever', 'health', 'appointment'],
            'name' => 'Sick Leave',
            'description' => 'Time off for medical reasons, illness, or health-related appointments'
        ],
        'bereavement' => [
            'keywords' => ['bereavement', 'death', 'funeral'],
            'name' => 'Bereavement Leave',
            'description' => 'Time off due to death of family member or close friend'
        ],
        'annual' => [
            'keywords' => ['vacation', 'holiday', 'annual', 'time off', 'personal', 'break', 'rest', 'travel', 'emergency'],
            'name' => 'Annual Leave',
            'description' => 'General vacation time, personal days, and planned time off'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Leave Request Settings
    |--------------------------------------------------------------------------
    | Configuration for leave request processing and validation.
    */

    'request_settings' => [
        'max_advance_days' => env('LEAVE_MAX_ADVANCE_DAYS', 365), // How far in advance can leave be requested
        'min_notice_days' => env('LEAVE_MIN_NOTICE_DAYS', 1), // Minimum notice required
        'auto_approve_threshold' => env('LEAVE_AUTO_APPROVE_DAYS', 0), // Auto-approve if under X days (0 = disabled)
        'require_attachment_for_sick' => env('LEAVE_REQUIRE_SICK_ATTACHMENT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    | Configuration for caching leave balance and attendance data.
    */

    'cache' => [
        'balance_ttl' => env('LEAVE_CACHE_BALANCE_TTL', 600), // 10 minutes
        'attendance_ttl' => env('LEAVE_CACHE_ATTENDANCE_TTL', 300), // 5 minutes
        'prefix' => env('LEAVE_CACHE_PREFIX', 'discord_attendance:'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Discord Integration Settings
    |--------------------------------------------------------------------------
    | Settings specific to Discord bot integration.
    */

    'discord' => [
        'rate_limit_requests_per_minute' => env('DISCORD_RATE_LIMIT_RPM', 60),
        'default_department' => env('DISCORD_DEFAULT_DEPARTMENT', 'General'),
        'admin_roles' => ['HR', 'Manager', 'Admin'], // Discord roles that can approve leave
        'notification_channels' => [
            'leave_requests' => env('DISCORD_LEAVE_CHANNEL_ID', null),
            'approvals' => env('DISCORD_APPROVAL_CHANNEL_ID', null),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Working Hours Configuration
    |--------------------------------------------------------------------------
    | Configuration for calculating working hours and overtime.
    */

    'working_hours' => [
        'standard_hours_per_day' => env('STANDARD_WORK_HOURS', 8),
        'standard_days_per_week' => env('STANDARD_WORK_DAYS', 5),
        'overtime_threshold' => env('OVERTIME_THRESHOLD_HOURS', 8),
        'break_deduction_enabled' => env('BREAK_DEDUCTION_ENABLED', true),
        'minimum_break_minutes' => env('MINIMUM_BREAK_MINUTES', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance Status Definitions
    |--------------------------------------------------------------------------
    | Valid attendance statuses and their descriptions.
    */

    'attendance_statuses' => [
        'checked_in' => 'Employee has checked in for the day',
        'checked_out' => 'Employee has checked out for the day',
        'on_break' => 'Employee is currently on break',
        'screen_sharing' => 'Employee is screen sharing (indicating active work)',
        'offline' => 'Employee is not currently active',
    ],

    /*
    |--------------------------------------------------------------------------
    | Leave Types
    |--------------------------------------------------------------------------
    | Valid leave types that can be requested.
    */

    'leave_types' => [
        'Full Day' => 'Full day leave (8 hours)',
        'Half Day' => 'Half day leave (4 hours)',
        'Emergency' => 'Emergency leave (processed urgently)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Half Day Periods
    |--------------------------------------------------------------------------
    | Available periods for half-day leave requests.
    */

    'half_day_periods' => [
        'First Half' => 'First half of the day (morning)',
        'Second Half' => 'Second half of the day (afternoon)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Employment Types and Leave Entitlements
    |--------------------------------------------------------------------------
    | Different employment types may have different leave entitlements.
    */

    'employment_entitlements' => [
        'full_time' => [
            'annual' => 25,
            'sick' => 12,
            'bereavement' => 5,
        ],
        'part_time' => [
            'annual' => 15, // Pro-rated
            'sick' => 8,
            'bereavement' => 3,
        ],
        'contract' => [
            'annual' => 20,
            'sick' => 5,
            'bereavement' => 2,
        ],
        'intern' => [
            'annual' => 10,
            'sick' => 5,
            'bereavement' => 2,
        ],
        'consultant' => [
            'annual' => 0, // Usually not entitled to paid leave
            'sick' => 0,
            'bereavement' => 0,
        ],
    ],
];
