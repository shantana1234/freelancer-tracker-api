<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'start_time',
        'end_time',
        'description',
        'hours',
        'tag',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
];

}
