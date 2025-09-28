<?php

namespace App\Rules;

use App\Models\Enrollment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEnrollment implements ValidationRule
{
    protected $studentId;
    protected $excludeId;

    public function __construct($studentId = null, $excludeId = null)
    {
        $this->studentId = $studentId;
        $this->excludeId = $excludeId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value || !$this->studentId) {
            return;
        }

        $query = Enrollment::where('course_id', $value)
            ->where('student_id', $this->studentId);

        // Exclude current record when editing
        if ($this->excludeId) {
            $query->where('id', '!=', $this->excludeId);
        }

        $existingEnrollment = $query->first();

        if ($existingEnrollment) {
            $course = \App\Models\Course::find($value);
            $student = \App\Models\Student::find($this->studentId);
            $fail("The student {$student->name} is already enrolled in the course '{$course->title}'. Please select a different course or student.");
        }
    }
}
