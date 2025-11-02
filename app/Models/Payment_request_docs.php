<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// app/Models/PaymentRequestDoc.php
// ... imports ...
class PaymentRequestDoc extends Model
{
    protected $fillable = ['user_id', 'file_name', 'file_path', 'file_type', 'file_size', 'uploaded_at'];

    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }
}
