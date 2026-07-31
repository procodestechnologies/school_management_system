<?php

namespace App\Services;

use Athwari\LaravelZktecoAdms\Models\ZktecoUser;
use Athwari\LaravelZktecoAdms\Services\DeviceManager;
use Athwari\LaravelZktecoAdms\Services\CommandManager;
use Athwari\LaravelZktecoAdms\DTOs\UserRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ZKTecoUserSyncService
{
    private DeviceManager $deviceManager;
    private CommandManager $commandManager;

    public function __construct(
        DeviceManager $deviceManager,
        CommandManager $commandManager
    ) {
        $this->deviceManager = $deviceManager;
        $this->commandManager = $commandManager;
    }

    /**
     * Add a user to a ZKTeco device.
     * 
     * @param string $deviceSerial The serial number of the device
     * @param array $userData User data with keys: pin, name, privilege, card, password
     * @return bool True if user was added/updated successfully
     */
    public function addUserToDevice(string $deviceSerial, array $userData): bool
    {
        try {
            // Validate required fields
            if (empty($userData['pin']) || empty($userData['name'])) {
                Log::error("PIN and Name are required to add user to device", [
                    'device' => $deviceSerial,
                    'user_data' => $userData
                ]);
                return false;
            }

            // Check if device exists
            $device = $this->deviceManager->getDevice($deviceSerial);
            if (!$device) {
                Log::error("Device not found", ['device' => $deviceSerial]);
                return false;
            }

            // Build only the parameters using tabs
            $paramParts = [
                sprintf("PIN=%s", (string) $userData['pin']),
                sprintf("Name=%s", $userData['name']),
                sprintf("Pri=%d", (int) ($userData['privilege'] ?? 0)),
            ];

            // Add Card if provided
            // if (!empty($userData['card'])) {
            //     $paramParts[] = sprintf("Card=%s", (string) $userData['card']);
            // }

            // Add Password if provided
            // Add Password ONLY if it is filled out, preserving leading zeros safely as a string
            if (isset($userData['password']) && $userData['password'] !== '') {
                // Optional: Validate for K40 Pro limits (1-6 digits)
                $cleanPassword = substr(preg_replace('/\D/', '', $userData['password']), 0, 6);

                if (!empty($cleanPassword)) {
                    $paramParts[] = sprintf("Password=%s", $cleanPassword);
                }
            }
            // FIX: Put a SPACE after the action verb, and separate parameters with tabs (\t)
            // Correct format: DATA UPDATE USERINFO PIN=101\tName=John\tPri=0
            $commandString = "DATA UPDATE USERINFO " . implode("\t", $paramParts);

            Log::debug("Queuing DATA UPDATE USERINFO command with password", [
                'device' => $deviceSerial,
                'command' => $commandString,
                'pin' => $userData['pin'],
                'password' => isset($userData['password']) && $userData['password'] !== '' ? 'set' : 'not set'
            ]);

            // Queue the command
            $this->commandManager->queueCommand($deviceSerial, $commandString);

            // Also store in local ZktecoUser model for reference
            ZktecoUser::updateOrCreate(
                ['pin' => (string) $userData['pin']],
                [
                    'name' => $userData['name'],
                    'card_number' => $userData['card'] ?? null,
                    'privilege' => (int) ($userData['privilege'] ?? 0),
                    'app_user_id' => $userData['app_user_id'] ?? null,
                ]
            );

            Log::info("User command queued successfully", [
                'device' => $deviceSerial,
                'pin' => $userData['pin'],
                'name' => $userData['name'],
                'password_set' => !empty($userData['password']),
                'card_set' => !empty($userData['card'])
            ]);

            // Clear cache for this device
            Cache::forget("device_users_{$deviceSerial}");

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to add user to device", [
                'device' => $deviceSerial,
                'pin' => $userData['pin'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }


    /**
     * Check if a user exists on a device using UserRecord DTO.
     * 
     * @param string $deviceSerial The serial number of the device
     * @param string $pin The PIN to check
     * @return bool True if the user exists on the device
     */
    public function userExistsOnDevice(string $deviceSerial, string $pin): bool
    {
        try {
            // Get the device
            $device = $this->deviceManager->getDevice($deviceSerial);
            if (!$device) {
                Log::warning("Device not found when checking user existence", [
                    'device' => $deviceSerial
                ]);
                return false;
            }

            // Check cache first
            $cacheKey = "device_users_{$deviceSerial}";
            $users = Cache::remember($cacheKey, 300, function () use ($deviceSerial, $device) {
                // Query all users from the device
                $this->commandManager->sendQueryUsersCommand($deviceSerial);

                // Get users from the local ZktecoUser model
                // These are synced when the device responds with USERINFO data
                return ZktecoUser::where('device_id', $device->id)->get();
            });

            // Check if the PIN exists in the user list
            $exists = $users->contains('pin', (string) $pin);

            if ($exists) {
                Log::debug("User exists on device", [
                    'device' => $deviceSerial,
                    'pin' => $pin
                ]);
            } else {
                Log::debug("User does not exist on device", [
                    'device' => $deviceSerial,
                    'pin' => $pin
                ]);
            }

            return $exists;
        } catch (\Exception $e) {
            Log::warning("Failed to check user existence on device", [
                'device' => $deviceSerial,
                'pin' => $pin,
                'error' => $e->getMessage()
            ]);
            return false; // Assume user doesn't exist
        }
    }

    /**
     * Get a user from the device by PIN.
     * 
     * @param string $deviceSerial The serial number of the device
     * @param string $pin The PIN to search for
     * @return UserRecord|null The user record or null if not found
     */
    public function getUserFromDevice(string $deviceSerial, string $pin): ?UserRecord
    {
        try {
            $device = $this->deviceManager->getDevice($deviceSerial);
            if (!$device) {
                Log::warning("Device not found when getting user", [
                    'device' => $deviceSerial
                ]);
                return null;
            }

            // Query users from device
            $this->commandManager->sendQueryUsersCommand($deviceSerial);

            // Get user from local model
            $zktecoUser = ZktecoUser::where('device_id', $device->id)
                ->where('pin', (string) $pin)
                ->first();

            if (!$zktecoUser) {
                Log::debug("User not found in local database", [
                    'device' => $deviceSerial,
                    'pin' => $pin
                ]);
                return null;
            }

            // Create UserRecord DTO
            return new UserRecord(
                pin: (string) $zktecoUser->pin,
                name: $zktecoUser->name ?? '',
                privilege: (int) ($zktecoUser->privilege ?? 0),
                card: $zktecoUser->card_number ?? '',
                password: '', // Password is not stored in the model for security
            );
        } catch (\Exception $e) {
            Log::error("Failed to get user from device", [
                'device' => $deviceSerial,
                'pin' => $pin,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get all users from a device.
     * 
     * @param string $deviceSerial The serial number of the device
     * @return UserRecord[] Array of UserRecord DTOs
     */
    public function getAllUsersFromDevice(string $deviceSerial): array
    {
        try {
            $device = $this->deviceManager->getDevice($deviceSerial);
            if (!$device) {
                Log::warning("Device not found when getting all users", [
                    'device' => $deviceSerial
                ]);
                return [];
            }

            // Query users from device
            $this->commandManager->sendQueryUsersCommand($deviceSerial);

            // Get all users from local model
            $zktecoUsers = ZktecoUser::where('device_id', $device->id)->get();

            // Convert to UserRecord DTOs
            $users = [];
            foreach ($zktecoUsers as $zktecoUser) {
                $users[] = new UserRecord(
                    pin: (string) $zktecoUser->pin,
                    name: $zktecoUser->name ?? '',
                    privilege: (int) ($zktecoUser->privilege ?? 0),
                    card: $zktecoUser->card_number ?? '',
                    password: '', // Password is not stored in the model for security
                );
            }

            Log::debug("Retrieved users from device", [
                'device' => $deviceSerial,
                'count' => count($users)
            ]);

            return $users;
        } catch (\Exception $e) {
            Log::error("Failed to get all users from device", [
                'device' => $deviceSerial,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
