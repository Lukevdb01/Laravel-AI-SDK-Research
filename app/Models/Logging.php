<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logging extends Model
{
    use HasFactory;

    protected $table = 'loggings';

    protected $fillable = [
        'ai_module_used',
        'prompt',
        'total_tokens_used',
        'response',
    ];

    protected $casts = [
        'response' => 'array',
        'total_tokens_used' => 'integer',
    ];
}
