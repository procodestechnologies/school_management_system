<?php

namespace App\Listeners;

use App\Models\Devices;
use App\Services\ZKTecoUserSyncService;
use Athwari\LaravelZktecoAdms\Events\DeviceConnected;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Student\Models\StudentDetails;

/**
 * The moment a device transitions from offline to online (see
 * DeviceManager::updateActivity()), push every active student belonging
 * to that device's institution onto it - so a device that was registered
 * while offline is fully populated as soon as it actually connects,
 * without anyone having to manually trigger a sync per student.
 */
class SyncStudentsToDeviceListener
{
    public function __construct(
        private readonly ZKTecoUserSyncService $syncService,
    ) {}

    public function handle(DeviceConnected $event): void
    {
        $serialNumber = $event->device->serial_number;

        $devices = Devices::where('serial_number', $serialNumber)
            ->where('is_active', true)
            ->get();

        foreach ($devices as $device) {
            $this->syncInstitutionStudents($device->institution_id, $serialNumber);
        }
    }

    private function syncInstitutionStudents(int $institutionId, string $serialNumber): void
    {
        $students = StudentDetails::where('institution_id', $institutionId)
            ->where('is_active', true)
            ->with('student')
            ->get();

        foreach ($students as $studentDetails) {
            $student = $studentDetails->student;

            if (! $student) {
                continue;
            }

            if ($this->syncService->userExistsOnDevice($serialNumber, (string) $student->id)) {
                continue;
            }

            $photoPath = $studentDetails->profile_photo && Storage::disk('public')->exists($studentDetails->profile_photo)
                ? Storage::disk('public')->path($studentDetails->profile_photo)
                : null;

            $synced = $this->syncService->addUserToDevice($serialNumber, [
                'pin' => (string) $student->id,
                'name' => $student->name,
                'privilege' => 0,
                'card' => $studentDetails->student_number ?? '',
                'password' => $studentDetails->admission_number ?? '',
                'app_user_id' => $student->id,
                'photo_path' => $photoPath,
            ]);

            $student->update($synced
                ? ['zkteco_synced' => true, 'zkteco_synced_at' => now(), 'zkteco_sync_error' => null]
                : ['zkteco_synced' => false, 'zkteco_sync_error' => 'Auto-sync on device connect failed']);

            Log::info('Auto-synced student to newly connected device', [
                'student_id' => $student->id,
                'device' => $serialNumber,
                'synced' => $synced,
            ]);
        }
    }
}
