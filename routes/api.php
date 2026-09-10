<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;


/*
|--------------------------------------------------------------------------
| Student Import API
|--------------------------------------------------------------------------
*/


// =========================================================
// IMPORT CSV
// =========================================================

Route::post(
    '/students/import-csv',
    [StudentController::class, 'importCSV']
);


// =========================================================
// IMPORT HISTORY
// =========================================================

Route::get(
    '/students/import-history',
    [StudentController::class, 'importHistory']
);


// =========================================================
// IMPORT HISTORY DETAILS
// =========================================================

Route::get(
    '/students/import-history/{id}',
    [StudentController::class, 'importHistoryDetails']
);