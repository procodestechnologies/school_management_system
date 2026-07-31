<?php

namespace Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Devices;
use Athwari\LaravelZktecoAdms\Models\ZktecoAttendanceLog;
use Illuminate\Http\Request;
use Modules\Institution\Models\Institution;

class AttendanceController extends Controller
{
    /**
     * List student attendance recorded on devices belonging to an institution.
     */
    public function institutionAttendance(Request $request, int $institution)
    {
        $institution = Institution::findOrFail($institution);

        $deviceIds = Devices::where('institution_id', $institution->id)
            ->when($request->filled('device_id'), fn ($q) => $q->where('id', $request->integer('device_id')))
            ->whereNotNull('zkteco_device_id')
            ->pluck('zkteco_device_id');

        $logs = ZktecoAttendanceLog::whereIn('device_id', $deviceIds)
            ->with(['device', 'zktecoUser.appUser.studentUserDetails'])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('occurred_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('occurred_at', '<=', $request->date('to')))
            ->orderByDesc('occurred_at')
            ->paginate($request->integer('per_page', 25));

        $logs->getCollection()->transform(function (ZktecoAttendanceLog $log) {
            $student = $log->zktecoUser?->appUser;
            $studentDetails = $student?->studentUserDetails;

            return [
                'id' => $log->id,
                'device' => [
                    'id' => $log->device?->id,
                    'serial_number' => $log->device?->serial_number,
                    'name' => $log->device?->name,
                ],
                'student' => $student ? [
                    'id' => $student->id,
                    'name' => $student->name,
                    'admission_number' => $studentDetails?->admission_number,
                    'student_number' => $studentDetails?->student_number,
                ] : null,
                'pin' => $log->pin,
                'status' => $log->status,
                'verify_mode' => $log->verify_mode,
                'recorded_at' => $log->recorded_at,
                'occurred_at' => $log->occurred_at,
            ];
        });

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
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('attendance::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('attendance::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('attendance::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('attendance::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
