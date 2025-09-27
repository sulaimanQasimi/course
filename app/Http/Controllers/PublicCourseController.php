<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class PublicCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::active()
            ->with(['category', 'teachers'])
            ->orderBy('start_date', 'asc');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by search term
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('fee', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('fee', '<=', $request->max_price);
        }

        $courses = $query->paginate(12);
        $categories = Category::active()->ordered()->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show(Course $course)
    {
        // Only show published and public courses
        if ($course->status !== 'published' || $course->visibility !== 'public') {
            abort(404);
        }

        $course->load(['category', 'teachers', 'activeSyllabi']);

        return view('courses.show', compact('course'));
    }
}