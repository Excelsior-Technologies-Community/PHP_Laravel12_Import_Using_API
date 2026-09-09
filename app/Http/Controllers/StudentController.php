<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ImportHistory;
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

    /**
     * Show students list.
     */
    public function index()
    {
        $students = Student::orderBy('id', 'asc')->get();

        $importHistories = ImportHistory::latest()->get();

        return view(
            'students.index',
            compact('students', 'importHistories')
        );
    }

    /**
     * Store new student.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        Student::create(
            $request->only('name', 'email')
        );

        return back()->with(
            'success',
            'Student added successfully.'
        );
    }

    /**
     * Delete student.
     */
    public function destroy($id)
    {
        Student::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Student deleted successfully.'
        );
    }

    // =========================
    // EXPORT
    // =========================

    /**
     * Export students as CSV.
     */
    public function exportCSV()
    {
        return Excel::download(
            new StudentsExport,
            'students.csv'
        );
    }

    /**
     * Export students as PDF.
     */
    public function exportPDF()
    {
        $students = Student::orderBy('id', 'asc')->get();

        $pdf = Pdf::loadView(
            'students.pdf',
            compact('students')
        );

        return $pdf->download('students.pdf');
    }

    // =========================
    // IMPORT API
    // =========================

    /**
     * Import students from CSV.
     */
    public function importCSV(Request $request)
    {
        /*
         * ==========================================
         * FILE VALIDATION
         * ==========================================
         */

        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120',
            ],
        ]);

        /*
         * ==========================================
         * CREATE IMPORT OBJECT
         * ==========================================
         */

        $import = new StudentsImport();

        /*
         * ==========================================
         * IMPORT CSV
         * ==========================================
         */

        Excel::import(
            $import,
            $request->file('file')
        );

        /*
         * ==========================================
         * GET IMPORT SUMMARY
         * ==========================================
         */

        $summary = $import->getSummary();

        /*
         * ==========================================
         * DETERMINE STATUS
         * ==========================================
         */

        if ($summary['failed'] > 0 || $summary['duplicates'] > 0) {
            $status = 'completed_with_errors';
        } else {
            $status = 'completed';
        }

        /*
         * ==========================================
         * SAVE IMPORT HISTORY
         * ==========================================
         */

        $history = ImportHistory::create([
            'file_name' => $request->file('file')->getClientOriginalName(),

            'total_rows' => $summary['total_rows'],

            'imported_rows' => $summary['imported'],

            'duplicate_rows' => $summary['duplicates'],

            'failed_rows' => $summary['failed'],

            'status' => $status,

            'error_details' => $summary['errors'],
        ]);

        /*
         * ==========================================
         * API RESPONSE
         * ==========================================
         */

        return response()->json([
            'status' => true,

            'message' => $summary['failed'] > 0 ||
                         $summary['duplicates'] > 0
                ? 'CSV import completed with some issues.'
                : 'CSV imported successfully.',

            'import_history_id' => $history->id,

            'summary' => [
                'total_rows' => $summary['total_rows'],
                'imported' => $summary['imported'],
                'duplicates' => $summary['duplicates'],
                'failed' => $summary['failed'],
            ],

            'errors' => $summary['errors'],
        ]);
    }

    // =========================
    // IMPORT HISTORY API
    // =========================

    /**
     * Get import history.
     */
    public function importHistory()
    {
        $histories = ImportHistory::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Import history fetched successfully.',
            'data' => $histories,
        ]);
    }
}