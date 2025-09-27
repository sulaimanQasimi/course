<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Course permissions
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',
            'courses.publish',
            
            // Category permissions
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            
            // Enrollment permissions
            'enrollments.view',
            'enrollments.create',
            'enrollments.edit',
            'enrollments.delete',
            'enrollments.approve',
            
            // Payment permissions
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'payments.refund',
            
            // Payroll permissions
            'payroll.view',
            'payroll.create',
            'payroll.edit',
            'payroll.delete',
            'payroll.generate',
            'payroll.pay',
            
            // User permissions
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Invoice permissions
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            
            // Discount permissions
            'discounts.view',
            'discounts.create',
            'discounts.edit',
            'discounts.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $courseManager = Role::firstOrCreate(['name' => 'course_manager']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $student = Role::firstOrCreate(['name' => 'student']);
        $finance = Role::firstOrCreate(['name' => 'finance']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            'courses.view', 'courses.create', 'courses.edit', 'courses.delete', 'courses.publish',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'enrollments.view', 'enrollments.create', 'enrollments.edit', 'enrollments.delete', 'enrollments.approve',
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete', 'payments.refund',
            'payroll.view', 'payroll.create', 'payroll.edit', 'payroll.delete', 'payroll.generate', 'payroll.pay',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'discounts.view', 'discounts.create', 'discounts.edit', 'discounts.delete',
        ]);

        $courseManager->givePermissionTo([
            'courses.view', 'courses.create', 'courses.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'enrollments.view', 'enrollments.approve',
            'users.view',
        ]);

        $teacher->givePermissionTo([
            'courses.view',
            'enrollments.view',
            'payroll.view',
        ]);

        $student->givePermissionTo([
            'courses.view',
            'enrollments.view',
        ]);

        $finance->givePermissionTo([
            'payments.view', 'payments.edit', 'payments.refund',
            'payroll.view', 'payroll.generate', 'payroll.pay',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'discounts.view', 'discounts.create', 'discounts.edit', 'discounts.delete',
        ]);

        // Create a super admin user
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('super_admin');

        // Create a teacher user
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'John Teacher',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $teacherUser->assignRole('teacher');

        // Create a student user
        $studentUser = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Jane Student',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $studentUser->assignRole('student');
    }
}
