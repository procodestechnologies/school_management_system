<?php

namespace App\Http\Controllers;

use App\Models\Devices;
use App\Models\User;
use App\Services\ZKTecoUserSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SyncStudentToDeviceController extends Controller
{
    private ZKTecoUserSyncService $syncService;

    public function __construct(ZKTecoUserSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

/**
 * Manually sync a student to all active devices.
 */
public function syncStudent(Request $request, $studentId)
{
    $student = User::with('studentUserDetails')->findOrFail($studentId);
    
    if (!$student->hasRole('Student')) {
        return response()->json([
            'success' => false,
            'message' => 'User is not a student'
        ], 422);
    }
    
        $devices = Devices::whereSerialNumber($student->studentInstitution->devices->first()->serial_number)->get();
    
    if ($devices->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No active devices found'
        ], 404);
    }
    
    $studentDetails = $student->studentUserDetails;
    
    // Prepare user data for device
    // IMPORTANT: Password = admission_number, Card = student_number
    $deviceUserData = [
        'pin' => (string) $student->id,
        'name' => $student->name,
        'privilege' => 0, // Normal user
        'card' => $studentDetails?->student_number ?? '', // Card number (RFID)
        'password' => $studentDetails?->admission_number ?? '', // Password = admission_number
        'app_user_id' => $student->id,
    ];
    Log::debug("Manual sync data", [
        'student_id' => $student->id,
        'pin' => $deviceUserData['pin'],
        'name' => $deviceUserData['name'],
        'card' => $deviceUserData['card'],
        'password' => $deviceUserData['password'] ? 'set' : 'not set'
    ]);
    
    $results = [];
    $syncedCount = 0;
    
    foreach ($devices as $device) {
        try {
            $exists = $this->syncService->userExistsOnDevice(
                $device->serial_number,
                (string) $student->id
            );
            
            if ($exists) {
                $results[$device->serial_number] = [
                    'status' => 'exists',
                    'message' => 'User already exists on this device'
                ];
                continue;
            }
            
            $result = $this->syncService->addUserToDevice(
                $device->serial_number,
                $deviceUserData
            );
            
            if ($result) {
                $syncedCount++;
                $results[$device->serial_number] = [
                    'status' => 'success',
                    'message' => 'User synced successfully'
                ];
            } else {
                $results[$device->serial_number] = [
                    'status' => 'failed',
                    'message' => 'Failed to sync user to device'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error("Manual sync failed for student", [
                'student_id' => $student->id,
                'device' => $device->serial_number,
                'error' => $e->getMessage()
            ]);
            
            $results[$device->serial_number] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    if ($syncedCount > 0) {
        $student->update([
            'zkteco_synced' => true,
            'zkteco_synced_at' => now(),
            'zkteco_sync_error' => null
        ]);
    } else {
        $student->update([
            'zkteco_synced' => false,
            'zkteco_sync_error' => 'Manual sync failed for all devices'
        ]);
    }
    
    return response()->json([
        'success' => $syncedCount > 0,
        'message' => $syncedCount > 0 ? "Student synced to {$syncedCount} device(s)" : 'Failed to sync to any device',
        'data' => [
            'student' => $student,
            'devices_synced' => $syncedCount,
            'total_devices' => $devices->count(),
            'results' => $results
        ]
    ]);
}
    /**
     * Sync a student to a specific device.
     */
    public function syncStudentToDevice(Request $request, $studentId, $deviceSerial)
    {
        $student = User::with('studentUserDetails')->findOrFail($studentId);

        if (!$student->hasRole('Student')) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a student'
            ], 422);
        }

        // Check if device exists and is active
        $device = Devices::where('serial_number', $deviceSerial)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found or inactive'
            ], 404);
        }

        // Prepare user data
        $studentDetails = $student->studentUserDetails;
        $deviceUserData = [
            'pin' => (string) $student->id,
            'name' => $student->name,
            'privilege' => 0,
            'card' => $studentDetails?->student_number ?? '',
            'password' => $studentDetails?->admission_number ?? '',
            'app_user_id' => $student->id,
        ];

        try {
            // Check if user exists
            $exists = $this->syncService->userExistsOnDevice(
                $device->serial_number,
                (string) $student->id
            );

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists on this device',
                    'data' => [
                        'student_id' => $student->id,
                        'device_serial' => $device->serial_number,
                        'status' => 'exists'
                    ]
                ]);
            }

            // Add user to device
            $result = $this->syncService->addUserToDevice(
                $device->serial_number,
                $deviceUserData
            );

            if ($result) {
                // Update student sync status
                $student->update([
                    'zkteco_synced' => true,
                    'zkteco_synced_at' => now(),
                    'zkteco_sync_error' => null
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Student synced successfully',
                    'data' => [
                        'student_id' => $student->id,
                        'device_serial' => $device->serial_number,
                        'pin' => $deviceUserData['pin'],
                        'status' => 'success'
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to sync student to device'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error("Manual sync failed", [
                'student_id' => $student->id,
                'device' => $device->serial_number,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error syncing student: ' . $e->getMessage()
            ], 500);
        }
    }
}
