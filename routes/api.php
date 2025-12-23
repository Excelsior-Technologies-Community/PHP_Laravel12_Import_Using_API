<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// CSV Import API
Route::post('/students/import-csv', [StudentController::class, 'importCSV']);
