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


// Bulk Delete
Route::post(
    '/students/bulk-delete',
    [StudentController::class, 'bulkDelete']
)->name('students.bulk-delete');


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