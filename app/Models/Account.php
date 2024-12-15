<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //use HasFactory;
 
    protected $fillable = [
        'owner_id',
        'currency_id',
        'account_number',
        'type',
        'balance',
    ];
 
    
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function transaction_senders()
    {
        return $this->hasMany(Transaction::class, 'sender_account');
    }

    public function transaction_receivers()
    {
        return $this->hasMany(Transaction::class, 'receiver_account');
    }
}
