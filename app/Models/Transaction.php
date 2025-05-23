<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'customer_id', 'amount',
        'payment_date', 'payment_method', 'reference', 'notes'
    ];

    // function so that transactions table can have foreign key to invoice id
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

     // function so that transactions table can have foreign key to customer id
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted(){
        static::created(function($transaction){
            Report::create([
                'source' => 'transaction',
                'source_id' => $transaction->id,
                'description' => 'transaksi baru ditambahkan'
            ]);
        });
    }
}
