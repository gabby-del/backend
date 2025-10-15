<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
    ];
}
