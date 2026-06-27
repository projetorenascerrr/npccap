<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    const STATUS_PRE_INSCRITO = 'pre-inscrito';
    const STATUS_INSCRITO     = 'inscrito';
    const STATUS_CONFIRMADO   = 'confirmado';
    const STATUS_CERTIFICADO  = 'certificado_emitido';

    protected $fillable = [
        'course_id',
        'student_user_id',
        'status',
        'frequency',
        'grade',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'frequency'   => 'decimal:2',
        'grade'       => 'decimal:2',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(StudentUser::class, 'student_user_id');
    }

    public function getNameAttribute()
    {
        return $this->studentUser ? $this->studentUser->name : null;
    }

    public function getCpfAttribute()
    {
        return $this->studentUser ? $this->studentUser->cpf : null;
    }

    public function getEmailAttribute()
    {
        return $this->studentUser ? $this->studentUser->email : null;
    }

    public function getBirthDateAttribute()
    {
        return $this->studentUser ? $this->studentUser->birth_date : null;
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    // Accessor para obter o primeiro certificado
    public function getCertificateAttribute()
    {
        return $this->certificates()->first();
    }

    // RN010 – Aprovação automática
    public function isApproved(): bool
    {
        $course = $this->relationLoaded('course') ? $this->course : $this->course()->first();

        if (! $course) {
            return false;
        }

        // Validação de frequência mínima se configurada no curso
        if ($course->minimum_frequency !== null) {
            if ($this->frequency === null || $this->frequency < $course->minimum_frequency) {
                return false;
            }
        }

        // Validação de nota mínima se configurada no curso
        if ($course->minimum_grade !== null) {
            if ($this->grade === null || $this->grade < $course->minimum_grade) {
                return false;
            }
        }

        return true;
    }

    // RN011 – Elegibilidade para emissão de certificado
    public function canIssueCertificate(): bool
    {
        $course = $this->relationLoaded('course') ? $this->course : $this->course()->first();

        if (! $course || ! $course->isEncerrado()) {
            return false;
        }

        return $this->isApproved();
    }
}
