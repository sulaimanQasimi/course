<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicCourseController;

Route::get('/', function () {
    return redirect()->route('courses.index');
});

// Public course routes
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('courses.show');
