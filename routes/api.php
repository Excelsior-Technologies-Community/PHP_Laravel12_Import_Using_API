<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Student Import API
|--------------------------------------------------------------------------
*/

// Import CSV
Route::post(
    '/students/import-csv',
    [StudentController::class, 'importCSV']
);

// Import History
Route::get(
    '/students/import-history',
    [StudentController::class, 'importHistory']
);