@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('courses.index') }}" class="hover:text-blue-600">Courses</a></li>
            <li>/</li>
            <li class="text-gray-900">{{ $course->title }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Course Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                <!-- Course Image -->
                <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white text-4xl font-bold">{{ $course->code }}</span>
                </div>

                <div class="p-6">
                    <!-- Category Badge -->
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                              style="background-color: {{ $course->category->color }}20; color: {{ $course->category->color }}">
                            {{ $course->category->name }}
                        </span>
                    </div>

                    <!-- Course Title -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $course->title }}</h1>
                    <p class="text-lg text-gray-600 mb-4">{{ $course->code }}</p>

                    <!-- Course Description -->
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $course->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Course Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Course Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Schedule</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Start Date:</span>
                                <span class="font-medium">{{ $course->start_date ? $course->start_date->format('M j, Y') : 'TBD' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">End Date:</span>
                                <span class="font-medium">{{ $course->end_date ? $course->end_date->format('M j, Y') : 'TBD' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Duration:</span>
                                <span class="font-medium">
                                    @if($course->start_date && $course->end_date)
                                        {{ $course->start_date->diffInDays($course->end_date) }} days
                                    @else
                                        TBD
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Capacity</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Spots:</span>
                                <span class="font-medium">{{ $course->capacity ?: 'Unlimited' }}</span>
                            </div>
                            @if($course->capacity)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Available:</span>
                                    <span class="font-medium">{{ $course->available_spots ?? $course->capacity }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructors -->
            @if($course->teachers->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Instructors</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($course->teachers as $teacher)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($teacher->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $teacher->name }}</p>
                                    <p class="text-sm text-gray-500">{{ ucfirst($teacher->pivot->role) }} Instructor</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Syllabus -->
            @if($course->activeSyllabi->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Course Syllabus</h2>
                    <div class="space-y-4">
                        @foreach($course->activeSyllabi as $syllabus)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="font-medium text-gray-900 mb-2">{{ $syllabus->title }}</h3>
                                @if($syllabus->content)
                                    <div class="prose max-w-none text-sm text-gray-600">
                                        {!! Str::limit(strip_tags($syllabus->content), 200) !!}
                                    </div>
                                @endif
                                @if($syllabus->file_path)
                                    <div class="mt-2">
                                        <a href="{{ $syllabus->file_url }}" 
                                           class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                            📄 Download Syllabus ({{ $syllabus->file_size_formatted }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Enrollment Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                <div class="text-center mb-6">
                    <div class="text-3xl font-bold text-gray-900 mb-2">${{ number_format($course->fee, 2) }}</div>
                    <p class="text-gray-600">One-time payment</p>
                </div>

                <!-- Course Status -->
                <div class="mb-6">
                    @if($course->is_upcoming)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                            <p class="text-blue-800 font-medium">Upcoming Course</p>
                            <p class="text-blue-600 text-sm">Starts {{ $course->start_date->format('M j, Y') }}</p>
                        </div>
                    @elseif($course->is_ongoing)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                            <p class="text-green-800 font-medium">Currently Running</p>
                            <p class="text-green-600 text-sm">Ends {{ $course->end_date->format('M j, Y') }}</p>
                        </div>
                    @elseif($course->is_completed)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                            <p class="text-gray-800 font-medium">Course Completed</p>
                            <p class="text-gray-600 text-sm">Ended {{ $course->end_date->format('M j, Y') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Enrollment Button -->
                <div class="space-y-3">
                    @if($course->capacity && $course->available_spots <= 0)
                        <button disabled class="w-full bg-gray-400 text-white px-4 py-3 rounded-md font-medium cursor-not-allowed">
                            Course Full
                        </button>
                        <p class="text-sm text-gray-500 text-center">This course has reached its capacity limit.</p>
                    @else
                        <button class="w-full bg-blue-600 text-white px-4 py-3 rounded-md font-medium hover:bg-blue-700 transition-colors">
                            Enroll Now
                        </button>
                        <p class="text-sm text-gray-500 text-center">Secure payment processing</p>
                    @endif
                </div>

                <!-- Course Features -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-medium text-gray-900 mb-3">What's Included</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            Lifetime access to course materials
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            Certificate of completion
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            Instructor support
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            Community access
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
