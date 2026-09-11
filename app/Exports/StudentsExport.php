<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Fetch students for CSV export.
     */
    public function collection()
    {
        $query = Student::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = $this->request->input('search');

        if ($search) {
            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'email',
                    'like',
                    '%' . $search . '%'
                );

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        $fromDate =
            $this->request->input('from_date');

        $toDate =
            $this->request->input('to_date');

        if ($fromDate) {
            $query->whereDate(
                'created_at',
                '>=',
                $fromDate
            );
        }

        if ($toDate) {
            $query->whereDate(
                'created_at',
                '<=',
                $toDate
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sortBy =
            $this->request->input(
                'sort_by',
                'id'
            );

        $sortOrder =
            $this->request->input(
                'sort_order',
                'asc'
            );

        $allowedColumns = [
            'id',
            'name',
            'email',
            'created_at',
        ];

        if (!in_array(
            $sortBy,
            $allowedColumns
        )) {
            $sortBy = 'id';
        }

        if (!in_array(
            $sortOrder,
            ['asc', 'desc']
        )) {
            $sortOrder = 'asc';
        }

        return $query
            ->select(
                'id',
                'name',
                'email',
                'phone',
                'address',
                'gender',
                'date_of_birth',
                'course',
                'class_name',
                'department',
                'status',
                'created_at'
            )
            ->orderBy(
                $sortBy,
                $sortOrder
            )
            ->get();
    }


    /**
     * CSV headings.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Address',
            'Gender',
            'Date of Birth',
            'Course',
            'Class',
            'Department',
            'Status',
            'Created At',
        ];
    }
}