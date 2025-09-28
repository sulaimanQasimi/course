<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\DiscountCode;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'published')->count();
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $totalTeachers = Teacher::count();
        $activeTeachers = Teacher::where('status', 'active')->count();
        $totalEnrollments = Enrollment::count();
        $activeEnrollments = Enrollment::where('status', 'enrolled')->count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $activeDiscountCodes = DiscountCode::where('is_active', true)->count();

        return [
            Stat::make('Total Courses', $totalCourses)
                ->description('Active: ' . $activeCourses)
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
            
            Stat::make('Total Students', $totalStudents)
                ->description('Active: ' . $activeStudents)
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            
            Stat::make('Total Teachers', $totalTeachers)
                ->description('Active: ' . $activeTeachers)
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
            
            Stat::make('Total Enrollments', $totalEnrollments)
                ->description('Active: ' . $activeEnrollments)
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
            
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('From completed payments')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            
            Stat::make('Total Invoices', $totalInvoices)
                ->description('Paid: ' . $paidInvoices)
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
            
            Stat::make('Active Discount Codes', $activeDiscountCodes)
                ->description('Currently available')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning'),
        ];
    }
}
