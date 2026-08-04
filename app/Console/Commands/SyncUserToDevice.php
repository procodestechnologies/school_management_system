<?php

namespace App\Console\Commands;

use App\Models\Devices;
use App\Services\ZKTecoUserSyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Student\Models\StudentDetails;

#[Signature('app:sync-user-to-device
    {--student= : Only sync the student with this user ID}
    {--institution= : Only sync students belonging to this institution ID}
    {--device= : Only sync to the device with this serial number}
    {--force : Push even if the student already exists on the device}')]
#[Description('Push active students to their institution\'s active ZKTeco device(s)')]
class SyncUserToDevice extends Command
{
    public function __construct(
        private readonly ZKTecoUserSyncService $syncService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $studentsQuery = StudentDetails::where('is_active', true)->with('student');

        if ($studentId = $this->option('student')) {
            $studentsQuery->where('student_id', $studentId);
        }

        if ($institutionId = $this->option('institution')) {
            $studentsQuery->where('institution_id', $institutionId);
        }

        $students = $studentsQuery->get();

        if ($students->isEmpty()) {
            $this->warn('No matching active students found.');

            return self::SUCCESS;
        }

        $devicesByInstitution = Devices::where('is_active', true)
            ->when($this->option('device'), fn ($query, $serial) => $query->where('serial_number', $serial))
            ->get()
            ->groupBy('institution_id');

        $force = $this->option('force');
        $synced = 0;
        $skipped = 0;
        $failed = 0;

        $this->withProgressBar($students, function (StudentDetails $studentDetails) use ($devicesByInstitution, $force, &$synced, &$skipped, &$failed) {
            $student = $studentDetails->student;

            if (! $student) {
                $skipped++;

                return;
            }

            $devices = $devicesByInstitution->get($studentDetails->institution_id, collect());

            if ($devices->isEmpty()) {
                $skipped++;

                return;
            }

            $photoPath = $studentDetails->profile_photo && Storage::disk('public')->exists($studentDetails->profile_photo)
                ? Storage::disk('public')->path($studentDetails->profile_photo)
                : null;

            $deviceUserData = [
                'pin' => (string) $student->id,
                'name' => $student->name,
                'privilege' => 0,
                'card' => $studentDetails->student_number ?? '',
                'password' => $studentDetails->admission_number ?? '',
                'app_user_id' => $student->id,
                'photo_path' => $photoPath,
            ];

            $attempted = false;
            $succeeded = false;

            foreach ($devices as $device) {
                if (! $force && $this->syncService->userExistsOnDevice($device->serial_number, (string) $student->id)) {
                    continue;
                }

                $attempted = true;

                if ($this->syncService->addUserToDevice($device->serial_number, $deviceUserData)) {
                    $succeeded = true;
                }
            }

            if ($succeeded) {
                $student->update([
                    'zkteco_synced' => true,
                    'zkteco_synced_at' => now(),
                    'zkteco_sync_error' => null,
                ]);
                $synced++;
            } elseif ($attempted) {
                $student->update([
                    'zkteco_synced' => false,
                    'zkteco_sync_error' => 'Manual bulk sync failed for all devices',
                ]);
                $failed++;
            } else {
                $skipped++;
            }
        });

        $this->newLine(2);
        $this->info("Synced: {$synced}, Skipped: {$skipped}, Failed: {$failed}");

        Log::info('Manual bulk ZKTeco sync completed', [
            'synced' => $synced,
            'skipped' => $skipped,
            'failed' => $failed,
        ]);

        return self::SUCCESS;
    }
}
