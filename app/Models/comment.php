<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_request_id',
        'user_id',
        'content',
        'is_clarification_request',
    ];

    // The user who wrote the comment
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // The payment request the comment belongs to
    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }
}