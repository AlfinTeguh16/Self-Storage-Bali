<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'tb_payments';

    protected $fillable = [
        'customer_id', 'method', 'transaction_file', 'is_deleted',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public $timestamps = true;
}
