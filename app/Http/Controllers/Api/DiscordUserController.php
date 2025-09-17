<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DiscordAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DiscordUserController extends Controller
{
    protected $attendanceService;

    public function __construct(DiscordAttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Create or update Discord user mapping
     */
    public function createOrUpdateMapping(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discord_user_id' => 'required|string',
            'discord_username' => 'required|string',
            'discord_display_name' => 'nullable|string',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $mapping = $this->attendanceService->getOrCreateDiscordMapping(
                $request->discord_user_id,
                $request->discord_username,
                $request->discord_display_name,
                $request->employee_id
            );

            return response()->json([
                'success' => true,
                'data' => $mapping,
                'message' => 'Discord user mapping created/updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating Discord mapping: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initialize yearly leave balances
     */
    public function initializeYearlyBalances(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2020|max:2050',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $this->attendanceService->initializeYearlyBalances($request->year);

            return response()->json([
                'success' => true,
                'message' => 'Yearly leave balances initialized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error initializing yearly balances: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Discord user mapping
     */
    public function getMapping(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discord_user_id' => 'required_without:discord_username|string',
            'discord_username' => 'required_without:discord_user_id|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $query = \App\Models\DiscordUserMapping::where('is_active', true);

            if ($request->discord_user_id) {
                $query->where('discord_user_id', $request->discord_user_id);
            } else {
                $query->where('discord_username', $request->discord_username);
            }

            $mapping = $query->with(['employee', 'leaveBalance'])->first();

            if ($mapping) {
                return response()->json([
                    'success' => true,
                    'data' => $mapping
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Discord user mapping not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching mapping: ' . $e->getMessage()
            ], 500);
        }
    }
}
