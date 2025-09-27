<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Models\DiscountCode;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories
        $categories = [
            [
                'name' => 'Programming',
                'slug' => 'programming',
                'description' => 'Learn various programming languages and frameworks',
                'color' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Frontend and backend web development courses',
                'color' => '#10B981',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Data Science',
                'slug' => 'data-science',
                'description' => 'Data analysis, machine learning, and AI courses',
                'color' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'UI/UX design, graphic design, and creative courses',
                'color' => '#EF4444',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Create sample courses
        $programmingCategory = Category::where('slug', 'programming')->first();
        $webDevCategory = Category::where('slug', 'web-development')->first();
        $dataScienceCategory = Category::where('slug', 'data-science')->first();
        $designCategory = Category::where('slug', 'design')->first();

        $admin = User::where('email', 'admin@example.com')->first();

        $courses = [
            [
                'title' => 'Complete Laravel Course',
                'code' => 'LAR101',
                'description' => 'Learn Laravel from beginner to advanced level with real-world projects.',
                'category_id' => $programmingCategory->id,
                'fee' => 299.99,
                'capacity' => 50,
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(90),
                'status' => 'published',
                'visibility' => 'public',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'React & Next.js Masterclass',
                'code' => 'REACT201',
                'description' => 'Build modern web applications with React and Next.js.',
                'category_id' => $webDevCategory->id,
                'fee' => 399.99,
                'capacity' => 30,
                'start_date' => now()->addDays(45),
                'end_date' => now()->addDays(105),
                'status' => 'published',
                'visibility' => 'public',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Python for Data Science',
                'code' => 'PYTHON301',
                'description' => 'Master Python for data analysis, visualization, and machine learning.',
                'category_id' => $dataScienceCategory->id,
                'fee' => 499.99,
                'capacity' => 25,
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(120),
                'status' => 'published',
                'visibility' => 'public',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'UI/UX Design Fundamentals',
                'code' => 'DESIGN101',
                'description' => 'Learn the principles of user interface and user experience design.',
                'category_id' => $designCategory->id,
                'fee' => 249.99,
                'capacity' => 40,
                'start_date' => now()->addDays(15),
                'end_date' => now()->addDays(75),
                'status' => 'published',
                'visibility' => 'public',
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Advanced JavaScript',
                'code' => 'JS201',
                'description' => 'Deep dive into advanced JavaScript concepts and modern ES6+ features.',
                'category_id' => $programmingCategory->id,
                'fee' => 349.99,
                'capacity' => 35,
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(80),
                'status' => 'draft',
                'visibility' => 'public',
                'created_by' => $admin->id,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(
                ['code' => $courseData['code']],
                $courseData
            );
        }

        // Create sample discount codes
        $discountCodes = [
            [
                'code' => 'WELCOME20',
                'name' => 'Welcome Discount',
                'description' => '20% off for new students',
                'type' => 'percentage',
                'value' => 20.00,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'SAVE50',
                'name' => 'Early Bird Special',
                'description' => '$50 off for early enrollment',
                'type' => 'fixed',
                'value' => 50.00,
                'minimum_amount' => 200.00,
                'usage_limit' => 50,
                'usage_limit_per_user' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'STUDENT10',
                'name' => 'Student Discount',
                'description' => '10% off for students',
                'type' => 'percentage',
                'value' => 10.00,
                'usage_limit' => null, // Unlimited
                'usage_limit_per_user' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($discountCodes as $discountData) {
            DiscountCode::firstOrCreate(
                ['code' => $discountData['code']],
                $discountData
            );
        }

        $this->command->info('Sample data created successfully!');
        $this->command->info('Categories: ' . Category::count());
        $this->command->info('Courses: ' . Course::count());
        $this->command->info('Discount Codes: ' . DiscountCode::count());
    }
}