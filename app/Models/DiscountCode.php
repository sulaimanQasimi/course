<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'usage_limit',
        'usage_count',
        'usage_limit_per_user',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_courses',
        'metadata',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'usage_limit_per_user' => 'integer',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
        'applicable_courses' => 'array',
        'metadata' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                    });
    }

    public function scopeAvailable($query)
    {
        return $query->valid()->where(function ($q) {
            $q->whereNull('usage_limit')->orWhereRaw('usage_count < usage_limit');
        });
    }

    public function getIsValidAttribute()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }
        
        if ($this->expires_at && $this->expires_at < $now) {
            return false;
        }

        return true;
    }

    public function getIsAvailableAttribute()
    {
        if (!$this->is_valid) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function getIsNotStartedAttribute()
    {
        return $this->starts_at && $this->starts_at > now();
    }

    public function getRemainingUsageAttribute()
    {
        if (!$this->usage_limit) {
            return null; // Unlimited
        }
        
        return max(0, $this->usage_limit - $this->usage_count);
    }

    public function getFormattedValueAttribute()
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        
        return '$' . number_format($this->value, 2);
    }

    public function calculateDiscount($amount)
    {
        if (!$this->is_available) {
            return 0;
        }

        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        }

        return min($this->value, $amount); // Fixed amount, but can't exceed the total
    }

    public function canBeUsedBy($userId)
    {
        if (!$this->is_available) {
            return false;
        }

        // Check if user has already used this code the maximum number of times
        $userUsageCount = Enrollment::where('user_id', $userId)
            ->where('discount_code', $this->code)
            ->count();

        return $userUsageCount < $this->usage_limit_per_user;
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
