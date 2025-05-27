<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppSecret extends Model
{
    use HasFactory;
    protected $fillable = [
        'router_id', 'profile_id', 'name', 'password', 'service'
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function profile()
    {
        return $this->belongsTo(PppProfiles::class, 'profile_id');
    }
}
