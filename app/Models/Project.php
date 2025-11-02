<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
    ];

    // --- Relationships ---

    /**
     * A Project belongs to a single Department.
     * (Defined by the foreign key `department_id` in the projects table).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * A Project can have many Payment Requests associated with it.
     */
    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PaymentRequest::class);
    }

    /**
     * A Project can be part of multiple Budgets (e.g., annual vs. project-specific).
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
