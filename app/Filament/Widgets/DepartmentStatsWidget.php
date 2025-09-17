<?php
// app/Filament/Widgets/DepartmentStatsWidget.php
namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DepartmentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // Get department statistics
        $departmentStats = Employee::join('employment_history', 'employees.id', '=', 'employment_history.employee_id')
            ->where('employees.status', 'active')
            ->select('employment_history.current_department as department', DB::raw('count(*) as count'))
            ->groupBy('employment_history.current_department')
            ->orderByDesc('count')
            ->get();

        $stats = [];

        // Get top 4 departments
        $topDepartments = $departmentStats->take(4);

        foreach ($topDepartments as $index => $dept) {
            $color = match ($index) {
                0 => 'success',
                1 => 'info',
                2 => 'warning',
                default => 'primary'
            };

            $stats[] = Stat::make($dept->department ?? 'Unassigned', $dept->count)
                ->description('Active employees')
                ->descriptionIcon('heroicon-m-users')
                ->color($color);
        }

        // If we have more than 4 departments, show "Others"
        if ($departmentStats->count() > 4) {
            $othersCount = $departmentStats->skip(4)->sum('count');
            $othersTotal = $departmentStats->count() - 4;

            $stats[] = Stat::make('Others', $othersCount)
                ->description("{$othersTotal} more departments")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('gray');
        }

        // If we have less than 5 stats, add total employees stat
        if (count($stats) < 5) {
            $totalActive = Employee::where('status', 'active')->count();
            $stats[] = Stat::make('Total Active', $totalActive)
                ->description('All departments')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary');
        }

        return $stats;
    }
}
