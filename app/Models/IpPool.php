<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpPool extends Model
{
    use HasFactory;

     protected $fillable = [
        'router_id', 'name', 'range'
    ];

    public function router(){
        return $this->belongsTo(Router::class);
    }
}
