<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction_category extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'name'
    ];
 
    
    public function transactions() : HasMany
    {
        return $this-> hasMany(Transaction::class, 'category_id');
    }

}
