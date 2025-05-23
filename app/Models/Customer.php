<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
       'name', 'username', 'phone', 'address',
        'package', 'group', 'join_date', 'status', 'due_date', 'notes'
    ];

    public function getPackage(){
        return $this->belongsTo(Package::class, 'package');
    }

    // function so that the invoice table can have a foreign key that refers to the customer id
    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

    // function so that the transaction table can have a foreign key that refers to the customer id
    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function lastInvoices(){
        return $this->hasOne(Invoice::class)->latestOfMany('paid_at');
    }

    public function getDueDate(){
        return $this->hasOne(Invoice::class)->latestOfMany('due_date');
    }

    protected static function booted()
    {
        static::created(function($customer){
            Report::create([
                'source' => 'customer',
                'source_id' => $customer->id,
                'description' => 'customer baru ditambahkan'
            ]);
        }); 
    }
}
