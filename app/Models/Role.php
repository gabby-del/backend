<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     * This ensures the permissions column is returned as a PHP array/object.
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    // --- Relationship ---

    /**
     * A Role can be assigned to many Users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
