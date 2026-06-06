<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateLog extends Model
{
    public $timestamps = false;

    const ACTION_ISSUED    = 'issued';
    const ACTION_REISSUED  = 'reissued';
    const ACTION_CANCELLED = 'cancelled';
    const ACTION_APPROVED  = 'approved';

    protected $fillable = [
        'certificate_id',
        'action',
        'notes',
        'performed_by',
        'performed_at',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }
}
