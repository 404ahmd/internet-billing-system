<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'host', 'port', 'username', 'password',
        'is_online', 'last_seen_at'
    ];
}
