<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
abstract class BaseUser extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['email', 'password'];

}
