<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
 
    protected $fillable = [
        'name'
    ];
 
    
    public function transactions() : HasMany
    {
        return $this-> hasMany(Transaction::class, 'category_id');
    }

}
