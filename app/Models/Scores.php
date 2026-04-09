<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scores extends Model
{
    protected $table = 'scores';
    protected $fillable = [
        'user_id',
        'score',
        'max_score',
        'activity_type',
        'location'
    ];
}
