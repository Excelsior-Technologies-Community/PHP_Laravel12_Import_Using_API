<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    protected $fillable = [
        'file_name',
        'total_rows',
        'imported_rows',
        'duplicate_rows',
        'failed_rows',
        'status',
        'error_details',
    ];

    protected $casts = [
        'error_details' => 'array',
    ];
}