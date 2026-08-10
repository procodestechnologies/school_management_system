<?php

namespace Modules\ReportCard\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Classes\Models\SchoolClass;
use Modules\Institution\Models\Institution;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'student_id',
        'class_id',
        'term',
        'academic_year',
        'term_number',
        'mean_percentage',
        'mean_grade',
        'status',
        'pdf_path',
        'download_token',
        'completed_at',
        'sent_at',
        'downloaded_at',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'term_number' => 'integer',
        'mean_percentage' => 'decimal:2',
        'completed_at' => 'datetime',
        'sent_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Whether the one-time download link has already been spent.
     */
    public function isDownloaded(): bool
    {
        return $this->downloaded_at !== null;
    }

    /**
     * Issue a fresh download token, replacing any previous one - so a
     * re-send invalidates the link from the earlier delivery rather than
     * leaving two working links out in the wild.
     */
    public function issueDownloadToken(): string
    {
        $token = Str::random(32);

        $this->update(['download_token' => $token, 'downloaded_at' => null]);

        return $token;
    }
}
