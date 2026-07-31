<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $zktecoDevicesTable = config('zkteco-adms.table_prefix', 'zkteco_').'devices';

        Schema::table('devices', function (Blueprint $table) use ($zktecoDevicesTable) {
            $table->foreignId('zkteco_device_id')->nullable()->after('serial_number')
                ->constrained($zktecoDevicesTable)->nullOnDelete();
        });

        if (Schema::hasTable($zktecoDevicesTable)) {
            DB::table('devices')->whereNull('zkteco_device_id')->orderBy('id')
                ->chunkById(200, function ($devices) use ($zktecoDevicesTable) {
                    foreach ($devices as $device) {
                        $zktecoDevice = DB::table($zktecoDevicesTable)
                            ->where('serial_number', $device->serial_number)
                            ->first();

                        if ($zktecoDevice) {
                            DB::table('devices')->where('id', $device->id)
                                ->update(['zkteco_device_id' => $zktecoDevice->id]);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('zkteco_device_id');
        });
    }
};
