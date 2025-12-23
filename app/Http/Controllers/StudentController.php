<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;

class StudentController extends Controller
{
    // =========================
    // WEB CRUD
    // =========================

    // Show students list 
    public function index()
    {
        $students = Student::orderBy('id', 'asc')->get();
        return view('students.index', compact('students'));
    }

    // Store new student
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email'
        ]);

        Student::create($request->only('name', 'email'));
        return back();
    }

    // Delete student
    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return back();
    }

    // =========================
    // EXPORT
    // =========================

    // Export students as CSV
    public function exportCSV()
    {
        return Excel::download(new StudentsExport, 'students.csv');
    }

    // Export students as PDF
    public function exportPDF()
    {
        $students = Student::orderBy('id', 'asc')->get();
        $pdf = Pdf::loadView('students.pdf', compact('students'));
        return $pdf->download('students.pdf');
    }

    // =========================
    // IMPORT (API)
    // =========================

    // Import students from CSV (API)
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
