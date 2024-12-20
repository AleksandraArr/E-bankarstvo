<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\BaseUser;
class Employee extends BaseUser
{

    use HasApiTokens; 

    protected $fillable = [
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password', 
    ];
}
