<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Budgets extends Model
{
    use HasFactory;
    protected $table = 'budgets';
    protected $fillable = [
        'department_id',
        'project_id',
        'budget_type',
        'fiscal_year',
        'total_amount',
        'allocated_amount',
        'spent_amount',
        'available_amount',
    ];
}
