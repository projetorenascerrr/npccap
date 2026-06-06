<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'hours',
        'course_date',
        'ass1',
        'ass2',
        'image_path',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
