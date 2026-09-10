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
    // =========================================================
    // STUDENT LIST
    // SEARCH + SORT + PAGINATION + DATE FILTER + TRASH
    // =========================================================

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = $request->input('search');

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sortBy = $request->input('sort_by', 'id');

        $sortOrder = $request->input('sort_order', 'asc');

        $allowedSortColumns = [
            'id',
            'name',
            'email',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        $fromDate = $request->input('from_date');

        $toDate = $request->input('to_date');

        /*
        |--------------------------------------------------------------------------
        | View Mode
        |--------------------------------------------------------------------------
        */

        $view = $request->input('view', 'active');

        /*
        |--------------------------------------------------------------------------
        | Student Query
        |--------------------------------------------------------------------------
        */

        $query = Student::query();

        /*
        |--------------------------------------------------------------------------
        | Trash
        |--------------------------------------------------------------------------
        */

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search) {
            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');

            });
        }

        /*
        |--------------------------------------------------------------------------
        | From Date
        |--------------------------------------------------------------------------
        */

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        /*
        |--------------------------------------------------------------------------
        | To Date
        |--------------------------------------------------------------------------
        */

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $students = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Import History
        |--------------------------------------------------------------------------
        */

        $importHistories = ImportHistory::oldest()->get();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalStudents = Student::count();

        $totalTrash = Student::onlyTrashed()->count();

        $todayStudents = Student::whereDate(
            'created_at',
            today()
        )->count();

        $thisWeekStudents = Student::whereBetween(
            'created_at',
            [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]
        )->count();

        return view(
            'students.index',
            compact(
                'students',
                'importHistories',
                'search',
                'sortBy',
                'sortOrder',
                'fromDate',
                'toDate',
                'view',
                'totalStudents',
                'totalTrash',
                'todayStudents',
                'thisWeekStudents'
            )
        );
    }


    // =========================================================
    // STORE STUDENT
    // =========================================================

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:students,email',
            ],
        ]);

        Student::create(
            $request->only(
                'name',
                'email'
            )
        );

        return back()->with(
            'success',
            'Student added successfully.'
        );
    }


    // =========================================================
    // SOFT DELETE
    // =========================================================

    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        $student->delete();

        return back()->with(
            'success',
            'Student moved to trash successfully.'
        );
    }


    // =========================================================
    // RESTORE STUDENT
    // =========================================================

    public function restore($id)
    {
        $student = Student::onlyTrashed()
            ->findOrFail($id);

        $student->restore();

        return back()->with(
            'success',
            'Student restored successfully.'
        );
    }


    // =========================================================
    // BULK DELETE
    // =========================================================

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'student_ids' => [
                'required',
                'array',
            ],

            'student_ids.*' => [
                'integer',
                'exists:students,id',
            ],
        ]);

        Student::whereIn(
            'id',
            $request->student_ids
        )->delete();

        return back()->with(
            'success',
            count($request->student_ids)
            . ' student(s) moved to trash.'
        );
    }


    // =========================================================
    // EXPORT CSV
    // =========================================================

    public function exportCSV(Request $request)
    {
        return Excel::download(
            new StudentsExport($request),
            'students.csv'
        );
    }


    // =========================================================
    // EXPORT PDF
    // =========================================================

    public function exportPDF()
    {
        $students = Student::orderBy(
            'id',
            'asc'
        )->get();

        $pdf = Pdf::loadView(
            'students.pdf',
            compact('students')
        );

        return $pdf->download(
            'students.pdf'
        );
    }


    // =========================================================
    // IMPORT CSV
    // =========================================================

    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120',
            ],
        ]);

        $import = new StudentsImport();

        Excel::import(
            $import,
            $request->file('file')
        );

        $summary = $import->getSummary();

        if (
            $summary['failed'] > 0 ||
            $summary['duplicates'] > 0
        ) {
            $status = 'completed_with_errors';
        } else {
            $status = 'completed';
        }

        $history = ImportHistory::create([
            'file_name' =>
                $request
                    ->file('file')
                    ->getClientOriginalName(),

            'total_rows' =>
                $summary['total_rows'],

            'imported_rows' =>
                $summary['imported'],

            'duplicate_rows' =>
                $summary['duplicates'],

            'failed_rows' =>
                $summary['failed'],

            'status' =>
                $status,

            'error_details' =>
                $summary['errors'],
        ]);

        return response()->json([
            'status' => true,

            'message' =>
                $summary['failed'] > 0 ||
                $summary['duplicates'] > 0
                    ? 'CSV import completed with some issues.'
                    : 'CSV imported successfully.',

            'import_history_id' =>
                $history->id,

            'summary' => [
                'total_rows' =>
                    $summary['total_rows'],

                'imported' =>
                    $summary['imported'],

                'duplicates' =>
                    $summary['duplicates'],

                'failed' =>
                    $summary['failed'],
            ],

            'errors' =>
                $summary['errors'],
        ]);
    }


    // =========================================================
    // IMPORT HISTORY API
    // =========================================================

    public function importHistory()
    {
        $histories =
            ImportHistory::oldest()->get();

        return response()->json([
            'status' => true,

            'message' =>
                'Import history fetched successfully.',

            'data' =>
                $histories,
        ]);
    }


    // =========================================================
    // IMPORT HISTORY DETAILS
    // =========================================================

    public function importHistoryDetails($id)
    {
        $history =
            ImportHistory::findOrFail($id);

        return response()->json([
            'status' => true,

            'message' =>
                'Import history details fetched successfully.',

            'data' => $history,
        ]);
    }
}