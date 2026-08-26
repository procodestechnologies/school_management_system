<?php

namespace Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use App\Models\Devices;
use Athwari\LaravelZktecoAdms\Models\ZktecoAttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Attendance\Services\AttendanceLogQuery;
use Modules\Institution\Models\Institution;

class AttendanceController extends Controller
{
    use Sortable;

    /**
     * List student attendance recorded on devices belonging to an institution.
     */
    public function institutionAttendance(Request $request, int $institution)
    {
        abort_unless(Auth::check() && Auth::user()->can('view attendance'), 403);

        $institution = Institution::findOrFail($institution);

        $logs = AttendanceLogQuery::build($institution->id, Auth::user(), $request->only(['device_id', 'search', 'from', 'to']))
            ->orderByDesc('occurred_at')
            ->paginate($request->integer('per_page', 25));

        $logs->getCollection()->transform(fn (ZktecoAttendanceLog $log) => $this->toApiRow($log));

        return response()->json([
            'success' => true,
            'institution' => [
                'id' => $institution->id,
                'name' => $institution->name,
            ],
            'data' => $logs,
        ]);
    }

    /**
     * Flatten a log into the JSON shape returned by the API endpoint.
     *
     * @return array<string, mixed>
     */
    private function toApiRow(ZktecoAttendanceLog $log): array
    {
        $student = $log->zktecoUser->appUser;
        $studentDetails = $student->studentUserDetails;

        return [
            'id' => $log->id,
            'device' => [
                'id' => $log->device?->id,
                'serial_number' => $log->device?->serial_number,
                'name' => $log->device?->name,
            ],
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'admission_number' => $studentDetails->admission_number,
                'student_number' => $studentDetails->student_number,
            ],
            'pin' => $log->pin,
            'status' => $log->status,
            'verify_mode' => $log->verify_mode,
            'recorded_at' => $log->recorded_at,
            'occurred_at' => $log->occurred_at,
        ];
    }
}
