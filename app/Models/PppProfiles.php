<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppProfiles extends Model
{
    use HasFactory;
    protected $fillable = [
         'router_id',
        'name',
        'local_address',
        'remote_address',
        'rate_limit',
    ];

   public function router()
    {
        return $this->belongsTo(Router::class);
    }

}
