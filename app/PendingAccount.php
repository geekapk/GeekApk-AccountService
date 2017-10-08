<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingAccount extends Model
{
    protected $fillable = ['name', 'password', 'email', 'email_token'];
}
