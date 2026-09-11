<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ImportHistory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $student = Student::create($this->validatedStudentData($request));

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Student added successfully.',
                'student' => $student,
            ]);
        }

        return back()->with('success', 'Student added successfully.');
    }

    public function create()
    {
        return view('students.create');
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);

        return view('students.show', compact('student'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);

        return view('students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update($this->validatedStudentData($request, $student));

        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Student updated successfully.',
                'student' => $student->fresh(),
            ]);
        }

        return redirect()->route('students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    public function searchSuggestions(Request $request)
    {
        $search = trim((string) $request->input('search'));

        if ($search === '') {
            return response()->json([]);
        }

        $students = Student::query()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'email']);

        return response()->json($students);
    }

    private function validatedStudentData(Request $request, ?Student $student = null): array
    {
        $emailRule = Rule::unique('students', 'email');

        if ($student) {
            $emailRule->ignore($student->id);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'date_of_birth' => ['nullable', 'date'],
            'course' => ['nullable', 'string', 'max:255'],
            'class_name' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Graduated'])],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('students', 'public');
        } else {
            unset($data['profile_photo']);
        }

        return $data;
    }


    // =========================================================
    // SOFT DELETE
    // =========================================================

    public function destroy(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $student->delete();

        return $this->actionResponse($request, 'Student moved to trash successfully.');
    }

    public function forceDestroy($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->forceDelete();

        return $this->actionResponse(request(), 'Student permanently deleted.');
    }


    // =========================================================
    // RESTORE STUDENT
    // =========================================================

    public function restore(Request $request, $id)
    {
        $student = Student::onlyTrashed()
            ->findOrFail($id);

        $student->restore();

        return $this->actionResponse($request, 'Student restored successfully.');
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

        return $this->actionResponse(
            $request,
            count($request->student_ids) . ' student(s) moved to trash.'
        );
    }

    public function bulkRestore(Request $request)
    {
        $ids = $this->validatedStudentIds($request);
        $count = Student::onlyTrashed()->whereIn('id', $ids)->restore();

        return $this->actionResponse($request, $count . ' student(s) restored.');
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $this->validatedStudentIds($request);
        $count = Student::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return $this->actionResponse($request, $count . ' student(s) permanently deleted.');
    }

    private function validatedStudentIds(Request $request): array
    {
        return $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ])['student_ids'];
    }

    private function actionResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
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