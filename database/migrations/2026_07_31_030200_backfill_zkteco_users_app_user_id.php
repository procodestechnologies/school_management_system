<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Historically, students were synced to devices with `pin` set to their
     * app users.id (see SyncStudentToDeviceController). Backfill the real
     * app_user_id FK from that convention so attendance can be joined properly.
     */
    public function up(): void
    {
        $zktecoUsersTable = config('zkteco-adms.table_prefix', 'zkteco_').'users';

        DB::table($zktecoUsersTable)->whereNull('app_user_id')->orderBy('id')
            ->chunkById(200, function ($zktecoUsers) use ($zktecoUsersTable) {
                foreach ($zktecoUsers as $zktecoUser) {
                    if (! ctype_digit((string) $zktecoUser->pin)) {
                        continue;
                    }

                    $userId = (int) $zktecoUser->pin;

                    if (DB::table('users')->where('id', $userId)->exists()) {
                        DB::table($zktecoUsersTable)->where('id', $zktecoUser->id)
                            ->update(['app_user_id' => $userId]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Data backfill only; not reversible.
    }
};
