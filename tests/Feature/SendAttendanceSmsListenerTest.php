<?php

use App\Listeners\SendAttendanceSmsListener;
use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Athwari\LaravelZktecoAdms\DTOs\AttendanceRecord;
use Athwari\LaravelZktecoAdms\Enums\AttendanceStatus;
use Athwari\LaravelZktecoAdms\Events\AttendanceReceived;
use Athwari\LaravelZktecoAdms\Models\ZktecoUser;
use Illuminate\Support\Facades\DB;
use Modules\Institution\Models\Institution;
use Modules\Student\Models\StudentDetails;

/**
 * @return array{0: User, 1: User} [$student, $parent]
 */
function makeAttendanceStudent(string $pin, ?string $parentPhone = '0712345678'): array
{
    $owner = User::factory()->create();
    $institution = Institution::create([
        'user_id' => $owner->id,
        'name' => 'Test Institution '.uniqid(),
        'code' => 'INST-'.uniqid(),
        'type' => 'School',
        'is_active' => true,
        'status' => 'active',
        'is_approved' => true,
    ]);

    $parent = User::factory()->create();
    // student_details.parent_id is (incorrectly) foreign-keyed to
    // parent_details.id rather than users.id - forcing them to match here
    // works around that pre-existing schema bug rather than fixing it.
    DB::table('parent_details')->insert([
        'id' => $parent->id,
        'parent_id' => $parent->id,
        'parent_phone' => $parentPhone,
    ]);

    $student = User::factory()->create(['name' => 'Jane Wanjiru']);
    StudentDetails::create([
        'student_id' => $student->id,
        'admission_number' => 'ADM-'.uniqid(),
        'parent_id' => $parent->id,
        'institution_id' => $institution->id,
    ]);

    ZktecoUser::create(['pin' => $pin, 'app_user_id' => $student->id]);

    return [$student, $parent];
}

function makeAttendanceRecord(string $pin, int $status = 0): AttendanceRecord
{
    return new AttendanceRecord($pin, new DateTimeImmutable('2026-08-08 07:45:00'), $status, 1, '', 'SN123');
}

test('it texts the parent when a student checks in', function () {
    Setting::set('sms', true);
    [$student, $parent] = makeAttendanceStudent('1001');
    $phone = DB::table('parent_details')->where('parent_id', $parent->id)->value('parent_phone');

    $this->mock(SmsService::class)
        ->shouldReceive('send')
        ->once()
        ->withArgs(function (int $mobile, string $message) use ($phone) {
            return $mobile === (int) $phone
                && str_contains($message, 'Jane Wanjiru')
                && str_contains($message, 'checked in');
        })
        ->andReturn(['success' => true]);

    app(SendAttendanceSmsListener::class)->handle(new AttendanceReceived('SN123', [makeAttendanceRecord('1001', AttendanceStatus::CheckIn->value)]));
});

test('it does nothing when the sms feature is disabled', function () {
    Setting::set('sms', false);
    makeAttendanceStudent('1002');

    $this->mock(SmsService::class)->shouldNotReceive('send');

    app(SendAttendanceSmsListener::class)->handle(new AttendanceReceived('SN123', [makeAttendanceRecord('1002')]));
});

test('it skips a punch from a pin with no linked app user', function () {
    Setting::set('sms', true);

    $this->mock(SmsService::class)->shouldNotReceive('send');

    app(SendAttendanceSmsListener::class)->handle(new AttendanceReceived('SN123', [makeAttendanceRecord('unknown-pin')]));
});

test('it skips a student whose parent has no phone on file', function () {
    Setting::set('sms', true);
    makeAttendanceStudent('1003', parentPhone: null);

    $this->mock(SmsService::class)->shouldNotReceive('send');

    app(SendAttendanceSmsListener::class)->handle(new AttendanceReceived('SN123', [makeAttendanceRecord('1003')]));
});
