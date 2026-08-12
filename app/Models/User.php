<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Modules\FeeManagement\Models\Fee;
use Modules\Institution\Models\Institution;
use Modules\Parent\Models\ParentDetails;
use Modules\Staff\Models\StaffDetails;
use Modules\Student\Models\StudentDetails;
use Modules\Teacher\Models\TeacherDetails;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'zkteco_synced', 'zkteco_synced_at', 'zkteco_sync_error', 'active_institution_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function children()
    {
        return $this->hasMany(StudentDetails::class, 'parent_id');
    }

    public function institution()
    {
        return $this->hasMany(Institution::class);
    }

    /**
     * The institution a Director is currently "running the system as" -
     * one of possibly several they own. See HasInstitution for how/when
     * this gets set.
     */
    public function activeInstitution()
    {
        return $this->belongsTo(Institution::class, 'active_institution_id');
    }

    public function studentUserDetails()
    {
        return $this->hasOne(StudentDetails::class, 'user_id');
    }

    public function parentUserDetails()
    {
        return $this->hasOneThrough(
            ParentDetails::class,        // Final model
            StudentDetails::class,     // Intermediate model
            'student_id',              // Foreign key on intermediate model (StudentDetails.student_id)
            'id',                      // Foreign key on final model (Institution.id)
            'id',                      // Local key on current model (User.id)
            'parent_id'           // Local key on intermediate model (StudentDetails.institution_id)
        );
    }

    public function parentInstitution()
    {
        return $this->hasOneThrough(
            Institution::class,
            StudentDetails::class,
            'parent_id',        // Foreign key on student_details (parent user id)
            'id',               // Foreign key on institutions (institution id)
            'id',               // Local key on users (parent user id)
            'institution_id'    // Local key on student_details (institution id)
        );
    }

    public function studentParent()
    {
        return $this->hasOneThrough(
            User::class,           // Final model (Parent User)
            StudentDetails::class, // Intermediate model
            'student_id',          // Foreign key on intermediate model (StudentDetails.student_id) - matches users.id
            'id',                  // Foreign key on final model (User.id) - matches StudentDetails.parent_id
            'id',                  // Local key on current model (User.id)
            'parent_id'            // Local key on intermediate model (StudentDetails.parent_id)
        );
    }

    public function studentInstitution()
    {
        return $this->hasOneThrough(
            Institution::class,        // Final model
            StudentDetails::class,     // Intermediate model
            'student_id',              // Foreign key on intermediate model (StudentDetails.student_id)
            'id',                      // Foreign key on final model (Institution.id)
            'id',                      // Local key on current model (User.id)
            'institution_id'           // Local key on intermediate model (StudentDetails.institution_id)
        );
    }

    public function parent()
    {
        return $this->hasOne(ParentDetails::class, 'parent_id');
    }

    public function fees()
    {
        return $this->hasMany(Fee::class, 'student_id');
    }

    public function childFees()
    {
        return $this->hasMany(Fee::class, 'parent_id');
    }

    public function teacherUserDetails()
    {
        return $this->hasOne(TeacherDetails::class, 'teacher_id');
    }

    /**
     * The staff record this login belongs to - how an Accountant is tied to
     * the school they work for, since they never own one.
     */
    public function staffUserDetails()
    {
        return $this->hasOne(StaffDetails::class, 'user_id');
    }
}
