<?php

namespace App\Models\Payments\Mpesa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STKTransactions extends Model
{
    use HasFactory;

    protected $table = 's_t_k_transactions';
}
