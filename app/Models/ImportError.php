<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportError extends Model
{
    protected $fillable = [
        'row_number',
        'row_data',
        'error_message',
    ];

    protected $casts = [
        'row_number' => 'integer',
    ];
}
