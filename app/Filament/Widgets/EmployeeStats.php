<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class EmployeeStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // Get current month and year for comparisons
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        // Total employees
        $totalEmployees = Employee::count();
        $previousMonthEmployees = Employee::whereDate('created_at', '<=', $previousMonth->endOfMonth())->count();
        $employeeGrowth = $totalEmployees - $previousMonthEmployees;

        // Active employees
        $activeEmployees = Employee::where('status', 'active')->count();
        $activePercentage = $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100, 1) : 0;

        // New hires this month
        $newHiresThisMonth = Employee::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        // Employees on probation
        $onProbation = Employee::whereHas('employmentHistory', function ($query) {
            $query->where('probation_end_date', '>', Carbon::now());
        })->count();

        // Department breakdown
        $departmentCount = Employee::whereHas('employmentHistory')
            ->with('employmentHistory')
            ->get()
            ->groupBy('employmentHistory.current_department')
            ->count();

        // Employment type breakdown
        $fullTimeEmployees = Employee::whereHas('employmentHistory', function ($query) {
            $query->where('employment_type', 'full_time');
        })->count();

        return [
            Stat::make('Total Employees', $totalEmployees)
                ->description($employeeGrowth > 0 ? "{$employeeGrowth} increase from last month" :
                    ($employeeGrowth < 0 ? abs($employeeGrowth) . " decrease from last month" : "No change from last month"))
                ->descriptionIcon($employeeGrowth > 0 ? 'heroicon-m-arrow-trending-up' :
                    ($employeeGrowth < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($employeeGrowth > 0 ? 'success' : ($employeeGrowth < 0 ? 'danger' : 'gray'))
                ->chart($this->getEmployeeGrowthChart()),

            Stat::make('Active Employees', $activeEmployees)
                ->description("{$activePercentage}% of total workforce")
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('New Hires', $newHiresThisMonth)
                ->description('This month')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make('On Probation', $onProbation)
                ->description('Employees in probation period')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Departments', $departmentCount)
                ->description('Active departments')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),

            Stat::make('Full-Time', $fullTimeEmployees)
                ->description(round(($fullTimeEmployees / max($totalEmployees, 1)) * 100, 1) . '% of workforce')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),
        ];
    }

    protected function getEmployeeGrowthChart(): array
    {
        // Get employee count for last 7 months
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Employee::whereDate('created_at', '<=', $date->endOfMonth())->count();
            $data[] = $count;
        }

        return $data;
    }
}
