<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'student_name',
        'cpf',
        'course_name',
        'issue_date',
        'validation_code',
        'qr_code',
        'reissue_count',
    ];

    protected $casts = [
        'issue_date'    => 'date',
        'reissue_count' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CertificateLog::class);
    }

    // RN012 – Gera código único no formato CERT-YYYY-NNNNNN
    public static function generateValidationCode(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        $seq  = str_pad((string) ($last + 1), 6, '0', STR_PAD_LEFT);

        return "CERT-{$year}-{$seq}";
    }

    // RN015 – Segunda via: reemissão mantém o mesmo código
    public function reissue(string $performedBy = 'system'): static
    {
        $this->increment('reissue_count');

        $this->logs()->create([
            'action'       => CertificateLog::ACTION_REISSUED,
            'notes'        => "Segunda via #" . $this->reissue_count,
            'performed_by' => $performedBy,
            'performed_at' => now(),
        ]);

        return $this->fresh();
    }
}
