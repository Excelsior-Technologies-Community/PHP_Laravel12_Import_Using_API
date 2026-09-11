<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'gender',
        'date_of_birth',
        'course',
        'class_name',
        'department',
        'status',
        'profile_photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];
}