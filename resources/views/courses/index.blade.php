@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Available Courses</h1>
        <p class="mt-2 text-gray-600">Discover and enroll in our comprehensive course offerings</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Search courses..." 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category" id="category" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Min Price -->
            <div>
                <label for="min_price" class="block text-sm font-medium text-gray-700">Min Price</label>
                <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" 
                       placeholder="0" min="0" step="0.01"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Max Price -->
            <div>
                <label for="max_price" class="block text-sm font-medium text-gray-700">Max Price</label>
                <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" 
                       placeholder="1000" min="0" step="0.01"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    <div class="mb-4">
        <p class="text-gray-600">
            Showing {{ $courses->count() }} of {{ $courses->total() }} courses
        </p>
    </div>

    <!-- Courses Grid -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Course Image Placeholder -->
                    <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">{{ $course->code }}</span>
                    </div>

                    <div class="p-6">
                        <!-- Category Badge -->
                        <div class="mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                  style="background-color: {{ $course->category->color }}20; color: {{ $course->category->color }}">
                                {{ $course->category->name }}
                            </span>
                        </div>

                        <!-- Course Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            <a href="{{ route('courses.show', $course) }}" class="hover:text-blue-600">
                                {{ $course->title }}
                            </a>
                        </h3>

                        <!-- Course Code -->
                        <p class="text-sm text-gray-500 mb-2">{{ $course->code }}</p>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ Str::limit($course->description, 120) }}
                        </p>

                        <!-- Course Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Start Date:</span>
                                <span class="font-medium">{{ $course->start_date ? $course->start_date->format('M j, Y') : 'TBD' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Duration:</span>
                                <span class="font-medium">
                                    @if($course->start_date && $course->end_date)
                                        {{ $course->start_date->diffInDays($course->end_date) }} days
                                    @else
                                        TBD
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Capacity:</span>
                                <span class="font-medium">
                                    @if($course->capacity)
                                        {{ $course->available_spots ?? $course->capacity }} / {{ $course->capacity }} spots
                                    @else
                                        Unlimited
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Teachers -->
                        @if($course->teachers->count() > 0)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 mb-1">Instructors:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($course->teachers->take(2) as $teacher)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                            {{ $teacher->name }}
                                        </span>
                                    @endforeach
                                    @if($course->teachers->count() > 2)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                            +{{ $course->teachers->count() - 2 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Price and Action -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($course->fee, 2) }}</span>
                            </div>
                            <a href="{{ route('courses.show', $course) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📚</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No courses found</h3>
            <p class="text-gray-500">Try adjusting your search criteria or check back later for new courses.</p>
        </div>
    @endif
</div>
@endsection
