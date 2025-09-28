<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'student_id_number',
        'enrollment_date',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
        'metadata' => 'array',
    ];

    // Enrollment relationships
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'enrolled');
    }

    public function completedEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'completed');
    }

    public function pendingEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'pending');
    }

    public function cancelledEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'cancelled');
    }

    // Course relationships through enrollments
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['status', 'enrolled_at', 'cancelled_at', 'amount_paid', 'discount_code', 'discount_amount'])
            ->withTimestamps();
    }

    public function activeCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('status', 'enrolled');
    }

    public function completedCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('status', 'completed');
    }

    // Payment relationships
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function completedPayments(): HasMany
    {
        return $this->payments()->where('status', 'completed');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->diffInYears(now()) : null;
    }

    public function getTotalEnrollmentsAttribute()
    {
        return $this->enrollments()->count();
    }

    public function getActiveEnrollmentsCountAttribute()
    {
        return $this->activeEnrollments()->count();
    }

    public function getTotalPaidAttribute()
    {
        return $this->completedPayments()->sum('amount');
    }
}
