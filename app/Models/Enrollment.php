<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'status',
        'enrolled_at',
        'cancelled_at',
        'cancellation_reason',
        'amount_paid',
        'discount_code',
        'discount_amount',
        'metadata',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function completedPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('status', 'completed');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWaitlisted($query)
    {
        return $query->where('status', 'waitlisted');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getIsEnrolledAttribute()
    {
        return $this->status === 'enrolled';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsWaitlistedAttribute()
    {
        return $this->status === 'waitlisted';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        $courseFee = $this->course->fee;
        $discountAmount = $this->discount_amount;
        $totalPaid = $this->total_paid;
        
        return max(0, $courseFee - $discountAmount - $totalPaid);
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->remaining_amount <= 0;
    }
}
