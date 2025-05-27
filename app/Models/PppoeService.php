<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppoeService extends Model
{
    use HasFactory;
     protected $fillable = [
        'router_id', 'interface', 'service_name'
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
}
