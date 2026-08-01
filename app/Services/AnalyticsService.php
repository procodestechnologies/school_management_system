<?php

namespace App\Services;

use App\Models\Devices;
use App\Models\User;
use Athwari\LaravelZktecoAdms\Models\ZktecoAttendanceLog;
use Illuminate\Support\Collection;
use Modules\FeeManagement\Models\Fee;
use Modules\Institution\Models\Institution;
use Modules\Student\Models\StudentDetails;

/**
 * Produces role-scoped analytics for the dashboard and the Report module,
 * so both surfaces read from a single source of truth.
 */
class AnalyticsService
{
    /**
     * Build the full stats payload for the given user, scoped to what their
     * role is allowed to see.
     */
    public function forUser(User $user): array
    {
        return match (true) {
            $user->hasRole('Admin') => $this->systemStats(),
            $user->hasRole('Director') => $this->institutionStats($user),
            $user->hasRole('Accountant') => $this->institutionStats($user, financeOnly: true),
            $user->hasRole('Parent') => $this->parentStats($user),
            $user->hasRole('Student') => $this->studentStats($user),
            $user->hasRole('Teacher') => $this->teacherStats($user),
            default => [],
        };
    }

    /**
     * Admin: platform-wide overview across every school.
     */
    public function systemStats(): array
    {
        $feeTotals = Fee::selectRaw('SUM(amount) as billed, SUM(amount_paid) as collected')->first();

        return [
            'scope' => 'system',
            'institutions_count' => Institution::count(),
            'active_institutions_count' => Institution::where('is_active', true)->count(),
            'users_by_role' => $this->usersByRole(),
            'students_count' => StudentDetails::count(),
            'devices_count' => Devices::count(),
            'attendance_today_count' => ZktecoAttendanceLog::whereDate('occurred_at', today())->count(),
            'fees_billed' => (float) ($feeTotals->billed ?? 0),
            'fees_collected' => (float) ($feeTotals->collected ?? 0),
            'fees_outstanding' => (float) ($feeTotals->billed ?? 0) - (float) ($feeTotals->collected ?? 0),
            'recent_institutions' => Institution::with('owner')->latest()->take(5)->get(),
            'enrollment_by_status' => StudentDetails::selectRaw('enrollment_status, COUNT(*) as total')
                ->groupBy('enrollment_status')
                ->pluck('total', 'enrollment_status'),
        ];
    }

    /**
     * Director/Accountant: everything scoped to the institutions they own.
     */
    public function institutionStats(User $user, bool $financeOnly = false): array
    {
        $institutionIds = $user->institution()->pluck('id');
        $institution = $user->institution()->first();

        $feeQuery = Fee::whereIn('institution_id', $institutionIds);
        $feeTotals = (clone $feeQuery)->selectRaw('SUM(amount) as billed, SUM(amount_paid) as collected')->first();

        $stats = [
            'scope' => 'institution',
            'institution' => $institution,
            'fees_billed' => (float) ($feeTotals->billed ?? 0),
            'fees_collected' => (float) ($feeTotals->collected ?? 0),
            'fees_outstanding' => (float) ($feeTotals->billed ?? 0) - (float) ($feeTotals->collected ?? 0),
            'overdue_fees_count' => (clone $feeQuery)->whereColumn('amount_paid', '<', 'amount')
                ->whereDate('due_date', '<', today())
                ->count(),
            'recent_fees' => (clone $feeQuery)->with('student')->latest()->take(5)->get(),
        ];

        if ($financeOnly) {
            return $stats;
        }

        $deviceIds = Devices::whereIn('institution_id', $institutionIds)
            ->whereNotNull('zkteco_device_id')
            ->pluck('zkteco_device_id');

        return $stats + [
            'students_count' => StudentDetails::whereIn('institution_id', $institutionIds)->count(),
            'parents_count' => $institution?->parents()->count() ?? 0,
            'devices_count' => Devices::whereIn('institution_id', $institutionIds)->count(),
            'attendance_today_count' => ZktecoAttendanceLog::whereIn('device_id', $deviceIds)
                ->whereDate('occurred_at', today())
                ->count(),
            'enrollment_by_status' => StudentDetails::whereIn('institution_id', $institutionIds)
                ->selectRaw('enrollment_status, COUNT(*) as total')
                ->groupBy('enrollment_status')
                ->pluck('total', 'enrollment_status'),
            'recent_students' => StudentDetails::whereIn('institution_id', $institutionIds)
                ->with('student')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Parent: summary across their children only.
     */
    public function parentStats(User $user): array
    {
        $children = StudentDetails::where('parent_id', $user->id)->with('student', 'institution')->get();
        $childIds = $children->pluck('student_id');

        $feeQuery = Fee::where('parent_id', $user->id);
        $feeTotals = (clone $feeQuery)->selectRaw('SUM(amount) as billed, SUM(amount_paid) as collected')->first();

        $deviceIds = Devices::whereIn('institution_id', $children->pluck('institution_id')->unique())
            ->whereNotNull('zkteco_device_id')
            ->pluck('zkteco_device_id');

        return [
            'scope' => 'parent',
            'children' => $children,
            'children_count' => $children->count(),
            'fees_billed' => (float) ($feeTotals->billed ?? 0),
            'fees_collected' => (float) ($feeTotals->collected ?? 0),
            'fees_outstanding' => (float) ($feeTotals->billed ?? 0) - (float) ($feeTotals->collected ?? 0),
            'attendance_today_count' => ZktecoAttendanceLog::whereIn('device_id', $deviceIds)
                ->whereIn('pin', $childIds->map(fn ($id) => (string) $id))
                ->whereDate('occurred_at', today())
                ->count(),
            'recent_fees' => (clone $feeQuery)->with('student')->latest()->take(5)->get(),
        ];
    }

    /**
     * Student: their own attendance and fee summary.
     */
    public function studentStats(User $user): array
    {
        $deviceIds = Devices::whereIn(
            'institution_id',
            StudentDetails::where('student_id', $user->id)->pluck('institution_id')
        )->whereNotNull('zkteco_device_id')->pluck('zkteco_device_id');

        $attendanceThisMonth = ZktecoAttendanceLog::whereIn('device_id', $deviceIds)
            ->where('pin', (string) $user->id)
            ->whereMonth('occurred_at', now()->month)
            ->whereYear('occurred_at', now()->year)
            ->count();

        $feeQuery = Fee::where('student_id', $user->id);
        $feeTotals = (clone $feeQuery)->selectRaw('SUM(amount) as billed, SUM(amount_paid) as collected')->first();

        return [
            'scope' => 'student',
            'attendance_this_month_count' => $attendanceThisMonth,
            'attendance_today_count' => ZktecoAttendanceLog::whereIn('device_id', $deviceIds)
                ->where('pin', (string) $user->id)
                ->whereDate('occurred_at', today())
                ->count(),
            'fees_billed' => (float) ($feeTotals->billed ?? 0),
            'fees_collected' => (float) ($feeTotals->collected ?? 0),
            'fees_outstanding' => (float) ($feeTotals->billed ?? 0) - (float) ($feeTotals->collected ?? 0),
            'recent_fees' => (clone $feeQuery)->latest()->take(5)->get(),
        ];
    }

    /**
     * Teacher: overview of the school they teach at.
     */
    public function teacherStats(User $user): array
    {
        $institution = $user->teacherUserDetails?->institution;

        if (! $institution) {
            return [
                'scope' => 'teacher',
                'institution' => null,
                'students_count' => 0,
                'attendance_today_count' => 0,
            ];
        }

        $deviceIds = Devices::where('institution_id', $institution->id)
            ->whereNotNull('zkteco_device_id')
            ->pluck('zkteco_device_id');

        return [
            'scope' => 'teacher',
            'institution' => $institution,
            'students_count' => StudentDetails::where('institution_id', $institution->id)->count(),
            'attendance_today_count' => ZktecoAttendanceLog::whereIn('device_id', $deviceIds)
                ->whereDate('occurred_at', today())
                ->count(),
        ];
    }

    /**
     * Count of users per role, platform-wide.
     */
    private function usersByRole(): Collection
    {
        return User::with('roles')
            ->get()
            ->flatMap(fn (User $user) => $user->getRoleNames())
            ->countBy()
            ->sortDesc();
    }
}
