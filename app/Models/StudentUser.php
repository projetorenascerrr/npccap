<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class StudentUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'student_users';

    protected $fillable = [
        'name',
        'cpf',
        'email',
        'birth_date',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'password' => 'hashed',
        ];
    }
}
