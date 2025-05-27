<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyNotification extends Model
{
    protected $fillable = ['user_id', 'date', 'type'];
}

