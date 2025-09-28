<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
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
        'employee_id',
        'hire_date',
        'department',
        'specialization',
        'qualifications',
        'bio',
        'profile_image',
        'status',
        'hourly_rate',
        'salary',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'qualifications' => 'array',
        'hourly_rate' => 'decimal:2',
        'salary' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Course relationships
    public function teachingCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_teachers')
            ->withPivot(['share_percentage', 'role', 'is_active', 'assigned_at'])
            ->withTimestamps();
    }

    public function activeTeachingCourses(): BelongsToMany
    {
        return $this->teachingCourses()->wherePivot('is_active', true);
    }

    public function createdCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    // Payroll relationships
    public function payrollRecords(): HasMany
    {
        return $this->hasMany(PayrollRecord::class);
    }

    public function recentPayrollRecords(): HasMany
    {
        return $this->payrollRecords()->orderBy('pay_period_end', 'desc');
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

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', $specialization);
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

    public function getTotalCoursesAttribute()
    {
        return $this->activeTeachingCourses()->count();
    }

    public function getTotalStudentsAttribute()
    {
        return $this->activeTeachingCourses()
            ->withCount('activeEnrollments')
            ->get()
            ->sum('active_enrollments_count');
    }

    public function getTotalEarningsAttribute()
    {
        return $this->payrollRecords()->sum('total_earnings');
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image ? asset('storage/' . $this->profile_image) : null;
    }

    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';
        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        return $initials;
    }
}
