<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'confirmation_key',
        'confirmation_expires',
    ];
};