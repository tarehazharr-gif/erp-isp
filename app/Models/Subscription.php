<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice; // Pastikan ini ada

class Subscription extends Model
{
    protected $fillable = ['customer_id', 'package_id', 'start_date', 'status'];

    // Ini fungsi untuk relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Ini fungsi untuk relasi ke Package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Ini fungsi automasi invoice (Pindahkan kode booted ke sini)
    protected static function booted()
    {
        static::created(function ($subscription) {
            // Kita ambil harga dari paket yang dipilih
            $price = $subscription->package->price;

            Invoice::create([
                'customer_id' => $subscription->customer_id,
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'amount' => $price,
                'due_date' => now()->addDays(7),
                'status' => 'unpaid',
            ]);
        });
    }
}