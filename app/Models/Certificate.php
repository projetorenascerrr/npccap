<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'student_name',
        'cpf',
        'course_name',
        'issue_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];
}
