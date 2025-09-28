<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'enrollment_id',
        'student_id',
        'course_id',
        'payment_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'status',
        'sent_at',
        'paid_at',
        'due_date',
        'notes',
        'pdf_path',
        'line_items',
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'due_date' => 'datetime',
        'line_items' => 'array',
        'metadata' => 'array',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function getIsDraftAttribute()
    {
        return $this->status === 'draft';
    }

    public function getIsSentAttribute()
    {
        return $this->status === 'sent';
    }

    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }


    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getPdfUrlAttribute()
    {
        if ($this->pdf_path) {
            return Storage::url($this->pdf_path);
        }
        return null;
    }

    public function getFormattedSubtotalAttribute()
    {
        return '$' . number_format($this->subtotal, 2);
    }

    public function getFormattedDiscountAmountAttribute()
    {
        return '$' . number_format($this->discount_amount, 2);
    }

    public function getFormattedTaxAmountAttribute()
    {
        return '$' . number_format($this->tax_amount, 2);
    }

    public function getFormattedTotalAmountAttribute()
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date < now() && !$this->is_paid;
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }
        
        return $this->due_date->diffInDays(now());
    }

    public function getFormattedDueDateAttribute()
    {
        if (!$this->due_date) {
            return null;
        }
        
        return $this->due_date->format('M j, Y');
    }

    public function getFormattedPaidDateAttribute()
    {
        if (!$this->paid_at) {
            return null;
        }
        
        return $this->paid_at->format('M j, Y');
    }
}
