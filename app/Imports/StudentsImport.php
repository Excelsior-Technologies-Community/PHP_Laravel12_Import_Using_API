<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    /**
     * Import statistics.
     */
    protected int $totalRows = 0;
    protected int $importedRows = 0;
    protected int $duplicateRows = 0;
    protected int $failedRows = 0;

    /**
     * Store row-level errors.
     */
    protected array $errors = [];

    /**
     * Store emails already processed in this CSV.
     */
    protected array $processedEmails = [];

    /**
     * Process CSV rows.
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            // Convert row to array
            $row = $row->toArray();

            // Ignore completely empty rows
            if (
                empty($row['name']) &&
                empty($row['email'])
            ) {
                continue;
            }

            $this->totalRows++;

            // Actual CSV row number.
            // Row 1 is heading, therefore data starts from row 2.
            $csvRowNumber = $index + 2;

            // Clean values
            $name = trim((string) ($row['name'] ?? ''));
            $email = strtolower(trim((string) ($row['email'] ?? '')));

            /*
             * ==========================================
             * VALIDATION
             * ==========================================
             */

            $validator = Validator::make(
                [
                    'name' => $name,
                    'email' => $email,
                ],
                [
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                    ],
                    'email' => [
                        'required',
                        'email',
                        'max:255',
                    ],
                    'gender' => ['nullable', 'in:Male,Female,Other'],
                    'date_of_birth' => ['nullable', 'date'],
                    'status' => ['nullable', 'in:Active,Inactive,Graduated'],
                ]
            );

            if ($validator->fails()) {

                $this->failedRows++;

                $this->errors[] = [
                    'row' => $csvRowNumber,
                    'name' => $name,
                    'email' => $email,
                    'reason' => implode(
                        ', ',
                        $validator->errors()->all()
                    ),
                ];

                continue;
            }

            /*
             * ==========================================
             * DUPLICATE CHECK INSIDE CURRENT CSV
             * ==========================================
             */

            if (in_array($email, $this->processedEmails, true)) {

                $this->duplicateRows++;

                $this->errors[] = [
                    'row' => $csvRowNumber,
                    'name' => $name,
                    'email' => $email,
                    'reason' => 'Duplicate email found in CSV file.',
                ];

                continue;
            }

            /*
             * ==========================================
             * DUPLICATE CHECK IN DATABASE
             * ==========================================
             */

            if (Student::where('email', $email)->exists()) {

                $this->duplicateRows++;

                $this->errors[] = [
                    'row' => $csvRowNumber,
                    'name' => $name,
                    'email' => $email,
                    'reason' => 'Email already exists in database.',
                ];

                // Mark email as processed
                $this->processedEmails[] = $email;

                continue;
            }

            /*
             * ==========================================
             * INSERT STUDENT
             * ==========================================
             */

            Student::create([
                'name' => $name,
                'email' => $email,
                'phone' => trim((string) ($row['phone'] ?? '')) ?: null,
                'address' => trim((string) ($row['address'] ?? '')) ?: null,
                'gender' => trim((string) ($row['gender'] ?? '')) ?: null,
                'date_of_birth' => $row['date_of_birth'] ?? null,
                'course' => trim((string) ($row['course'] ?? '')) ?: null,
                'class_name' => trim((string) ($row['class'] ?? $row['class_name'] ?? '')) ?: null,
                'department' => trim((string) ($row['department'] ?? '')) ?: null,
                'status' => trim((string) ($row['status'] ?? 'Active')) ?: 'Active',
            ]);

            $this->importedRows++;

            // Remember email
            $this->processedEmails[] = $email;
        }
    }

    /**
     * Return import statistics.
     */
    public function getSummary(): array
    {
        return [
            'total_rows' => $this->totalRows,
            'imported' => $this->importedRows,
            'duplicates' => $this->duplicateRows,
            'failed' => $this->failedRows,
            'errors' => $this->errors,
        ];
    }
}