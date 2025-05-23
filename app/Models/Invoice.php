<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'package_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'amount',
        'tax_amount',
        'total_amount',
        'paid_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    protected static function booted()
    {

        static::saving(function ($invoice) {
            if ($invoice->status === 'paid' && empty($invoice->paid_at)) {
                $invoice->paid_at = now();
            }
        });

        static::updated(function ($invoice) {
            if ($invoice->isDirty('status') && $invoice->status === 'paid') {

                Transaction::firstOrCreate(
                    ['invoice_id' => $invoice->id],
                    [
                        'customer_id' => $invoice->customer_id,
                        'amount' => $invoice->total_amount,
                        'payment_date' => $invoice->paid_at,
                        'payment_method' => "Cash",
                        'reference' => "INV-" . $invoice->invoice_number,
                        'notes' => "Pembayaran untuk invoice " . $invoice->invoice_number
                    ]
                );
            }
        });

        static::created(function($invoice){
            Report::create([
                'source' => 'invoice',
                'source_id' => $invoice->id,
                'description' => 'invoice baru ditambakan'
            ]);
        });
    }
}
