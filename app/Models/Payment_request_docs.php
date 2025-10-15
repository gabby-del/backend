<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Payment_request_docs extends Model
{
    use HasFactory;
    protected $table = 'payment_request_docs';
    protected $fillable = [
        'payment_request_id',
        'file_path',
        'uploaded_by',
    ];
}
