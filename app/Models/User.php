<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Course relationships
    public function createdCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

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

    // Payment relationships
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function completedPayments(): HasMany
    {
        return $this->payments()->where('status', 'completed');
    }

    // Invoice relationships
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function paidInvoices(): HasMany
    {
        return $this->invoices()->where('status', 'paid');
    }

    // Payroll relationships
    public function payrollRecords(): HasMany
    {
        return $this->hasMany(PayrollRecord::class, 'teacher_id');
    }

    public function paidPayrollRecords(): HasMany
    {
        return $this->payrollRecords()->where('status', 'paid');
    }

    // Syllabus relationships
    public function uploadedSyllabi(): HasMany
    {
        return $this->hasMany(CourseSyllabus::class, 'uploaded_by');
    }

    // Scopes
    public function scopeTeachers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'teacher');
        });
    }

    public function scopeStudents($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'student');
        });
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        });
    }

    // Helper methods
    public function getIsTeacherAttribute()
    {
        return $this->hasRole('teacher');
    }

    public function getIsStudentAttribute()
    {
        return $this->hasRole('student');
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    public function getTotalEarningsAttribute()
    {
        return $this->paidPayrollRecords()->sum('net_amount');
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->completedPayments()->sum('amount');
    }

    public function getTotalEnrollmentsAttribute()
    {
        return $this->activeEnrollments()->count();
    }
}
