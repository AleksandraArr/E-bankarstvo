<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //
    use HasFactory;

    protected $fillable = ['name', 'date', 'exchange_rate'];


    public function accounts() : HasMany
    {
       return $this->hasMany(Account::class);
    }

}
