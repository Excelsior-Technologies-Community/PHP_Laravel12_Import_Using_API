<!DOCTYPE html>
<html>
<head>
    <title>Student Data</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        h3 {
            margin-top: 30px;
            color: #333;
        }

        form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        input {
            padding: 8px;
            width: 30%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 8px 14px;
            background: #2563eb;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #1e40af;
        }

        .actions {
            margin-bottom: 20px;
        }

        .actions a {
            text-decoration: none;
            padding: 8px 12px;
            margin-right: 5px;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
        }

        .csv {
            background: #16a34a;
        }

        .pdf {
            background: #dc2626;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background: #f1f5f9;
        }

        table tr:nth-child(even) {
            background: #f9fafb;
        }

        .delete-btn {
            background: #dc2626;
            padding: 6px 10px;
            font-size: 13px;
        }

        .delete-btn:hover {
            background: #991b1b;
        }

        /* =========================
           STATISTICS
        ========================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            padding: 18px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .stat-card h4 {
            margin: 0 0 8px;
            color: #64748b;
            font-size: 14px;
        }

        .stat-card p {
            margin: 0;
            font-size: 25px;
            font-weight: bold;
            color: #1e293b;
        }

        /* =========================
           STATUS
        ========================= */

        .status {
            padding: 5px 9px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .completed {
            background: #dcfce7;
            color: #166534;
        }

        .completed-with-errors {
            background: #fef3c7;
            color: #92400e;
        }

        .success-message {
            padding: 10px;
            margin-bottom: 15px;
            background: #dcfce7;
            color: #166534;
            border-radius: 5px;
        }

        @media(max-width: 800px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 500px) {
            .stats {
                grid-template-columns: 1fr;
            }

            form {
                flex-direction: column;
            }

            input {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>🎓 Student Data</h2>

    {{-- Success message --}}
    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    {{-- =========================
         STUDENT FORM
    ========================== --}}

    <form method="POST" action="{{ route('students.store') }}">
        @csrf

        <input
            name="name"
            placeholder="Name"
            required
        >

        <input
            name="email"
            type="email"
            placeholder="Email"
            required
        >

        <button type="submit">
            Add Student
        </button>
    </form>

    {{-- =========================
         EXPORT
    ========================== --}}

    <div class="actions">

        <a
            href="{{ route('students.csv') }}"
            class="csv"
        >
            ⬇ Export CSV
        </a>

        <a
            href="{{ route('students.pdf') }}"
            class="pdf"
        >
            📄 Export PDF
        </a>

    </div>

    {{-- =========================
         IMPORT STATISTICS
    ========================== --}}

    @php

        $totalImports = $importHistories->count();

        $totalImported = $importHistories->sum(
            'imported_rows'
        );

        $totalDuplicates = $importHistories->sum(
            'duplicate_rows'
        );

        $totalFailed = $importHistories->sum(
            'failed_rows'
        );

    @endphp

    <div class="stats">

        <div class="stat-card">
            <h4>Total Imports</h4>
            <p>{{ $totalImports }}</p>
        </div>

        <div class="stat-card">
            <h4>Imported Students</h4>
            <p>{{ $totalImported }}</p>
        </div>

        <div class="stat-card">
            <h4>Duplicate Rows</h4>
            <p>{{ $totalDuplicates }}</p>
        </div>

        <div class="stat-card">
            <h4>Failed Rows</h4>
            <p>{{ $totalFailed }}</p>
        </div>

    </div>

    {{-- =========================
         STUDENTS TABLE
    ========================== --}}

    <h3>👨‍🎓 Students</h3>

    <table>

        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>

        @forelse($students as $s)

            <tr>

                <td>
                    {{ $s->id }}
                </td>

                <td>
                    {{ $s->name }}
                </td>

                <td>
                    {{ $s->email }}
                </td>

                <td>

                    <form
                        method="POST"
                        action="{{ route('students.delete', $s->id) }}"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="delete-btn"
                            onclick="return confirm('Delete this student?')"
                        >
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="4">
                    No students found.
                </td>
            </tr>

        @endforelse

    </table>

    {{-- =========================
         IMPORT HISTORY
    ========================== --}}

    <h3>📜 CSV Import History</h3>

    <table>

        <tr>
            <th>ID</th>
            <th>File Name</th>
            <th>Total Rows</th>
            <th>Imported</th>
            <th>Duplicates</th>
            <th>Failed</th>
            <th>Status</th>
            <th>Date</th>
        </tr>

        @forelse($importHistories as $history)

            <tr>

                <td>
                    {{ $history->id }}
                </td>

                <td>
                    {{ $history->file_name }}
                </td>

                <td>
                    {{ $history->total_rows }}
                </td>

                <td>
                    {{ $history->imported_rows }}
                </td>

                <td>
                    {{ $history->duplicate_rows }}
                </td>

                <td>
                    {{ $history->failed_rows }}
                </td>

                <td>

                    @if($history->status === 'completed')

                        <span class="status completed">
                            Completed
                        </span>

                    @else

                        <span class="status completed-with-errors">
                            Completed With Issues
                        </span>

                    @endif

                </td>

                <td>
                    {{ $history->created_at->format('d M Y, h:i A') }}
                </td>

            </tr>

        @empty

            <tr>

                <td colspan="8">
                    No CSV import history available.
                </td>

            </tr>

        @endforelse

    </table>

</div>

</body>
</html>