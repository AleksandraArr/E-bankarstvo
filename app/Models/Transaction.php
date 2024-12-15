<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'sender_account',
        'receiver_account',
        'date',
        'amount',
        'description',
        'status',
        'category_id'
    ];

    public function sender()
    {
        return $this->belongsTo(Account::class, 'sender_account');
    }

    public function receiver()
    {
        return $this->belongsTo(Account::class, 'receiver_account');
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    protected $casts = [
        'amount' => 'decimal:2', 
    ];
    
}
