<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppProfiles extends Model
{
    use HasFactory;
    protected $fillable = [
        'router_id', 'ip_pool_id', 'name', 'local_address', 'remote_address'
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function ipPool()
    {
        return $this->belongsTo(IpPool::class);
    }

    public function secrets()
    {
        return $this->hasMany(PppSecret::class, 'profile_id');
    }
}
