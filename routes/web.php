<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// Student CRUD
Route::get(
    '/students',
    [StudentController::class, 'index']
);

Route::post(
    '/students/store',
    [StudentController::class, 'store']
)->name('students.store');

Route::delete(
    '/students/delete/{id}',
    [StudentController::class, 'destroy']
)->name('students.delete');

// Export
Route::get(
    '/students/export-csv',
    [StudentController::class, 'exportCSV']
)->name('students.csv');

Route::get(
    '/students/export-pdf',
    [StudentController::class, 'exportPDF']
)->name('students.pdf');