<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function transaction_category()
    {
        return $this->belongsTo(Transaction_category::class, 'category_id');
    }
}
