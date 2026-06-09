<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    // RN006 – Status do evento
    const STATUS_ATIVO     = 'ativo';
    const STATUS_ENCERRADO = 'encerrado';
    const STATUS_CANCELADO = 'cancelado';

    protected $fillable = [
        'name',
        'description',
        'hours',
        'course_date',
        'start_date',
        'end_date',
        'responsible',
        'minimum_frequency',
        'minimum_grade',
        'status',
        'ass1',
        'ass2',
        'image_path',
        'image_bg',
    ];

    protected $casts = [
        'course_date'       => 'date',
        'start_date'        => 'date',
        'end_date'          => 'date',
        'hours'             => 'integer',
        'minimum_frequency' => 'decimal:2',
        'minimum_grade'     => 'decimal:2',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    // RN006 – Certificados só podem ser emitidos para eventos encerrados
    public function isEncerrado(): bool
    {
        return $this->status === self::STATUS_ENCERRADO;
    }
}
