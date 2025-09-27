<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'enrollment_id',
        'user_id',
        'course_id',
        'amount',
        'currency',
        'gateway',
        'gateway_payment_id',
        'gateway_transaction_id',
        'status',
        'captured_at',
        'refunded_amount',
        'refunded_at',
        'failure_reason',
        'gateway_response',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'captured_at' => 'datetime',
        'refunded_amount' => 'decimal:2',
        'refunded_at' => 'datetime',
        'gateway_response' => 'array',
        'metadata' => 'array',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopePartiallyRefunded($query)
    {
        return $query->where('status', 'partially_refunded');
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }

    public function getIsRefundedAttribute()
    {
        return $this->status === 'refunded';
    }

    public function getIsPartiallyRefundedAttribute()
    {
        return $this->status === 'partially_refunded';
    }

    public function getNetAmountAttribute()
    {
        return $this->amount - $this->refunded_amount;
    }

    public function getIsFullyRefundedAttribute()
    {
        return $this->refunded_amount >= $this->amount;
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . strtoupper($this->currency);
    }
}
