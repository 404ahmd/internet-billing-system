<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
     use HasFactory;
    protected $fillable = [
         'name', 'description', 'price', 'type', 'cycle', 'bandwidth', 'status'
    ];

     // function so that the invoice table can have a foreign key that refers to the package id
    public function invoices(){
        return $this->hasMany(Invoice::class);
    }
}
