<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $table = 'payment';

    public $fillable = [
        'order_id',
        'snap_token',
        'status',
        'expired_at',
        'paid_at',
    ];
}
