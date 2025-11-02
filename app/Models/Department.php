<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable (for creating/updating).
     */
    protected $fillable = [
        'name',
        'code',
    ];

    // --- Relationships ---

    /**
     * A Department can have many Users assigned to it.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

  
    /**
     * A Department can manage multiple Projects.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * A Department can have multiple Budgets defined.
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
