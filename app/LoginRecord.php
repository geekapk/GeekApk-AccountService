<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginRecord extends Model
{
    protected $fillable = ['user_id', 'ip', 'user_agent'];
}
