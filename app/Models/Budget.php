<?php

// app/Models/Budget.php
// ... imports ...

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = ['department_id', 'project_id','budget_type', 'fiscal_year', 'total_amount', 'allocated_amount', 'spent_amount', 'available_amount'];

    // Define reverse relationships...
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    // ...
}
