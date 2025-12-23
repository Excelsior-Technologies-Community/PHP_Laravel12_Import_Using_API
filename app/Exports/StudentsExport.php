<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    // Fetch students for CSV export
    public function collection()
    {
        return Student::select('id','name','email','created_at')
                      ->orderBy('id','asc')
                      ->get();
    }

    // CSV headings
    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Created At'];
    }
}
