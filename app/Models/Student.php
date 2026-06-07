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
        'name',
        'cpf',
        'email',
        'birth_date',
        'status',
        'frequency',
        'grade',
        'approved_at',
    ];

    protected $casts = [
        'birth_date'  => 'date',
        'approved_at' => 'datetime',
        'frequency'   => 'decimal:2',
        'grade'       => 'decimal:2',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    // RN010 – Aprovação automática
    public function isApproved(): bool
    {
        $course = $this->relationLoaded('course') ? $this->course : $this->course()->first();

        if (! $course) {
            return false;
        }

        return true;
    }

    // RN011 – Elegibilidade para emissão de certificado
    public function canIssueCertificate(): bool
    {
        return $this->isApproved();
    }
}
