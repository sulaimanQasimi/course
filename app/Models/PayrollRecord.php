<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    protected $fillable = [
        'teacher_id',
        'period_start',
        'period_end',
        'gross_amount',
        'deductions',
        'net_amount',
        'status',
        'generated_at',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
        'breakdown',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'gross_amount' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'generated_at' => 'datetime',
        'paid_at' => 'datetime',
        'breakdown' => 'array',
        'metadata' => 'array',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeGenerated($query)
    {
        return $query->where('status', 'generated');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function getIsDraftAttribute()
    {
        return $this->status === 'draft';
    }

    public function getIsGeneratedAttribute()
    {
        return $this->status === 'generated';
    }

    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getFormattedGrossAmountAttribute()
    {
        return '$' . number_format($this->gross_amount, 2);
    }

    public function getFormattedDeductionsAttribute()
    {
        return '$' . number_format($this->deductions, 2);
    }

    public function getFormattedNetAmountAttribute()
    {
        return '$' . number_format($this->net_amount, 2);
    }

    public function getPeriodDurationAttribute()
    {
        return $this->period_start->diffInDays($this->period_end) + 1;
    }

    public function getPeriodFormattedAttribute()
    {
        return $this->period_start->format('M j') . ' - ' . $this->period_end->format('M j, Y');
    }
}
