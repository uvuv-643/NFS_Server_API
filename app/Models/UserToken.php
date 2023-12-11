<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $token string
 * @property $ip string
 * @property $id integer
 */
class UserToken extends Model
{
    protected $guarded = [];

}
