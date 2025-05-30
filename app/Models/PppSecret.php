<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppSecret extends Model
{
    use HasFactory;

    protected $fillable = [
        'router_id','name', 'password', 'service', 'profile', 'local_address', 'remote_address', 'comment'
    ];

    public function router(){
        return $this->belongsTo(Router::class);
    }
}
