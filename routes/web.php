<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;


/*
|--------------------------------------------------------------------------
| Student Web Routes
|--------------------------------------------------------------------------
*/

// Student List
Route::get(
    '/students',
    [StudentController::class, 'index']
)->name('students.index');


// Add Student
Route::post(
    '/students',
    [StudentController::class, 'store']
)->name('students.store');

Route::get(
    '/students/create',
    [StudentController::class, 'create']
)->name('students.create');

Route::get(
    '/students/search-suggestions',
    [StudentController::class, 'searchSuggestions']
)->name('students.search-suggestions');

Route::get(
    '/students/{id}',
    [StudentController::class, 'show']
)->name('students.show');

Route::get(
    '/students/{id}/edit',
    [StudentController::class, 'edit']
)->name('students.edit');

Route::put(
    '/students/{id}',
    [StudentController::class, 'update']
)->name('students.update');

// Soft Delete
Route::delete(
    '/students/{id}',
    [StudentController::class, 'destroy']
)->name('students.delete');


// Restore
Route::post(
    '/students/{id}/restore',
    [StudentController::class, 'restore']
)->name('students.restore');

Route::delete(
    '/students/{id}/force',
    [StudentController::class, 'forceDestroy']
)->name('students.force-delete');


// Bulk Delete
Route::post(
    '/students/bulk-delete',
    [StudentController::class, 'bulkDelete']
)->name('students.bulk-delete');

Route::post(
    '/students/bulk-restore',
    [StudentController::class, 'bulkRestore']
)->name('students.bulk-restore');

Route::post(
    '/students/bulk-force-delete',
    [StudentController::class, 'bulkForceDelete']
)->name('students.bulk-force-delete');


// CSV Export
Route::get(
    '/students/export/csv',
    [StudentController::class, 'exportCSV']
)->name('students.csv');


// PDF Export
Route::get(
    '/students/export/pdf',
    [StudentController::class, 'exportPDF']
)->name('students.pdf');