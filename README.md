# PHP_Laravel12_Import_Using_API

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php">
  <img src="https://img.shields.io/badge/CSV-Import-success?style=for-the-badge">
  <img src="https://img.shields.io/badge/API-REST-blue?style=for-the-badge">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql">
</p>

---

##  Overview

The project helps you understand:
- API-based file uploads in Laravel  
- Bulk data import using **maatwebsite/excel**  
- Proper use of Import classes with Controllers  
- Clean MVC and API structure  

This repository is **beginner-friendly**, well-structured, and suitable for:
- Learning Laravel APIs  
- Practicing CSV imports  
- Interview demos  
- Academic or real-world backend projects  

---

---

##  Features

- Laravel 12
- CSV Import using API
- Uses maatwebsite/excel
- Header-based CSV import
- JSON API response
- Postman testing
- Beginner friendly

---

##  Folder Structure

```text
student-import/
│
├── app/
│   ├── Imports/
│   │   └── StudentsImport.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── StudentController.php
│   └── Models/
│       └── Student.php
│
├── database/
│   └── migrations/
│       └── xxxx_create_students_table.php
│
├── routes/
│   └── api.php
│
├── .env
├── composer.json
└── README.md
```

---

##  STEP 1: Installation

```bash
composer create-project laravel/laravel student-import
```

---

##  STEP 2: Database Configuration

Update `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=import
DB_USERNAME=root
DB_PASSWORD=
```

Create database manually:

```
import
```

---

##  STEP 3: Install Excel Package

```bash
composer require maatwebsite/excel
```

---

##  STEP 4: Student Model & Migration

```bash
php artisan make:model Student -m
```

### database/migrations/xxxx_create_students_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
```

Run migration:

```bash
php artisan migrate
```

---

##  STEP 5: Student Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['name', 'email'];
}
```

---

##  STEP 6: Import Class

```bash
php artisan make:import StudentsImport
```

### app/Imports/StudentsImport.php

```php
<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Student([
            'name'  => $row['name'],
            'email' => $row['email'],
        ]);
    }
}
```

---

##  STEP 7: Controller (API)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class StudentController extends Controller
{
    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return response()->json([
            'status' => true,
            'message' => 'CSV imported successfully'
        ]);
    }
}
```

---

##  STEP 8: API Route

### routes/api.php

```php
use App\Http\Controllers\StudentController;

Route::post('/students/import-csv', [StudentController::class, 'importCSV']);
```

---

##  STEP 9: CSV File Format

<img width="392" height="147" alt="Screenshot 2025-12-23 141017" src="https://github.com/user-attachments/assets/7d278ea1-e318-481d-bf10-93ade1b27187" />


 Column names must match exactly.

---

##  STEP 10: Test API (Postman)

### Endpoint

```
POST http://127.0.0.1:8000/api/students/import-csv
```

### Body → form-data

| Key  | Type | Value |
|------|------|-------|
| file | File | student.csv |

### Success Response

```json
{
  "status": true,
  "message": "CSV imported successfully"
}
```
<img width="1785" height="642" alt="Screenshot 2025-12-23 140822" src="https://github.com/user-attachments/assets/8b420c41-e835-4fd0-bf9e-7e05f94adc86" />

### OUTPUT:-

<img width="1038" height="573" alt="Screenshot 2025-12-23 140911" src="https://github.com/user-attachments/assets/6dcb21c8-e4c7-4d90-95a4-d1a60dad3035" />


---

