<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicCourseController;
use App\Http\Controllers\InvoicePrintController;

Route::get('/', function () {
    return redirect()->route('courses.index');
});

// Public course routes
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [PublicCourseController::class, 'show'])->name('courses.show');

// Invoice print route
Route::get('/invoices/{invoice}/print', [InvoicePrintController::class, 'print'])->name('invoices.print');
