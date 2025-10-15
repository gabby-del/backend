<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Budgets extends Model
{
    use HasFactory;
    protected $table = 'cost_centers';
    protected $fillable = [
        'name',
        'code',
        'department_id',
    ];
}
