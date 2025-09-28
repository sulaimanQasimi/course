<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    protected $fillable = [
        'title',
        'code',
        'description',
        'category_id',
        'fee',
        'capacity',
        'start_date',
        'end_date',
        'status',
        'visibility',
        'thumbnail_path',
        'teacher_id',
        'metadata',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'course_teachers')
            ->withPivot(['share_percentage', 'role', 'is_active', 'assigned_at'])
            ->withTimestamps();
    }

    public function activeTeachers(): BelongsToMany
    {
        return $this->teachers()->wherePivot('is_active', true);
    }

    public function syllabi(): HasMany
    {
        return $this->hasMany(CourseSyllabus::class);
    }

    public function activeSyllabi(): HasMany
    {
        return $this->syllabi()->where('is_active', true);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'enrolled');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function completedPayments(): HasMany
    {
        return $this->payments()->where('status', 'completed');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published')
                    ->where('visibility', 'public');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return Storage::url($this->thumbnail_path);
        }
        return null;
    }

    public function getIsUpcomingAttribute()
    {
        return $this->start_date && $this->start_date > now();
    }

    public function getIsOngoingAttribute()
    {
        return $this->start_date && $this->end_date && 
               $this->start_date <= now() && $this->end_date >= now();
    }

    public function getIsCompletedAttribute()
    {
        return $this->end_date && $this->end_date < now();
    }

    public function getAvailableSpotsAttribute()
    {
        if (!$this->capacity) {
            return null; // Unlimited capacity
        }
        
        $enrolled = $this->activeEnrollments()->count();
        return max(0, $this->capacity - $enrolled);
    }

    public function getTotalRevenueAttribute()
    {
        return $this->completedPayments()->sum('amount');
    }

    public function getTotalEnrollmentsAttribute()
    {
        return $this->activeEnrollments()->count();
    }
}
