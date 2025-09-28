<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample teachers
        \App\Models\Teacher::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'sarah.johnson@example.com',
            'phone' => '+1-555-0101',
            'employee_id' => 'T001',
            'hire_date' => now()->subYears(3),
            'department' => 'Computer Science',
            'specialization' => 'Web Development',
            'qualifications' => ['PhD Computer Science', 'MSc Software Engineering'],
            'bio' => 'Experienced web developer with expertise in modern frameworks.',
            'status' => 'active',
            'hourly_rate' => 75.00,
        ]);

        \App\Models\Teacher::create([
            'name' => 'Prof. Michael Chen',
            'email' => 'michael.chen@example.com',
            'phone' => '+1-555-0102',
            'employee_id' => 'T002',
            'hire_date' => now()->subYears(5),
            'department' => 'Data Science',
            'specialization' => 'Machine Learning',
            'qualifications' => ['PhD Data Science', 'MSc Statistics'],
            'bio' => 'Data science expert with focus on machine learning applications.',
            'status' => 'active',
            'hourly_rate' => 85.00,
        ]);

        // Create sample students
        \App\Models\Student::create([
            'name' => 'Alice Smith',
            'email' => 'alice.smith@example.com',
            'phone' => '+1-555-0201',
            'student_id_number' => 'S001',
            'enrollment_date' => now()->subMonths(6),
            'status' => 'active',
        ]);

        \App\Models\Student::create([
            'name' => 'Bob Wilson',
            'email' => 'bob.wilson@example.com',
            'phone' => '+1-555-0202',
            'student_id_number' => 'S002',
            'enrollment_date' => now()->subMonths(3),
            'status' => 'active',
        ]);

        \App\Models\Student::create([
            'name' => 'Carol Davis',
            'email' => 'carol.davis@example.com',
            'phone' => '+1-555-0203',
            'student_id_number' => 'S003',
            'enrollment_date' => now()->subMonths(1),
            'status' => 'active',
        ]);
    }
}
