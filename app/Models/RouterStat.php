<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouterStat extends Model
{
    use HasFactory;

    protected $fillable = ['router_id', 'cpu_load', 'memory_usage', 'logged_at'];
    
    protected $casts = [
        'logged_at' => 'datetime'
    ];
}
