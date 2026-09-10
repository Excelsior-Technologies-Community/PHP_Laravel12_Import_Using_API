<!DOCTYPE html>
<html>

<head>

    <title>Student Data Management</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        h3 {
            margin-top: 30px;
            color: #333;
        }

        input,
        select {
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button,
        .btn {
            padding: 9px 14px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .blue {
            background: #2563eb;
        }

        .green {
            background: #16a34a;
        }

        .red {
            background: #dc2626;
        }

        .orange {
            background: #ea580c;
        }

        .gray {
            background: #64748b;
        }

        .purple {
            background: #7c3aed;
        }

        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

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
        }

        .stat-card p {
            margin: 0;
            font-size: 25px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f1f5f9;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .success-message {
            padding: 12px;
            margin-bottom: 15px;
            background: #dcfce7;
            color: #166534;
            border-radius: 5px;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 7px 11px;
            margin-right: 4px;
            border: 1px solid #ddd;
            text-decoration: none;
            border-radius: 4px;
        }

        .selected {
            background: #2563eb;
            color: white;
        }

        .top-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .action-form {
            display: inline;
        }

        .small-btn {
            padding: 6px 9px;
            font-size: 12px;
        }

        @media(max-width: 800px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            table {
                font-size: 13px;
            }
        }

        @media(max-width: 500px) {

            .stats {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 15px;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        <h2>🎓 Student Data Management</h2>


        {{-- ================================================= --}}
        {{-- SUCCESS MESSAGE --}}
        {{-- ================================================= --}}

        @if(session('success'))

        <div class="success-message">

            {{ session('success') }}

        </div>

        @endif


        {{-- ================================================= --}}
        {{-- ADD STUDENT --}}
        {{-- ================================================= --}}

        <form
            method="POST"
            action="{{ route('students.store') }}"
            class="filters">

            @csrf

            <input
                name="name"
                placeholder="Student Name"
                required>

            <input
                name="email"
                type="email"
                placeholder="Student Email"
                required>

            <button
                type="submit"
                class="blue">
                ➕ Add Student
            </button>

        </form>


        {{-- ================================================= --}}
        {{-- FILTERS --}}
        {{-- ================================================= --}}

        <form
            method="GET"
            action="{{ route('students.index') }}"
            class="filters">

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="🔎 Search name/email">

            <input
                type="date"
                name="from_date"
                value="{{ $fromDate }}">

            <input
                type="date"
                name="to_date"
                value="{{ $toDate }}">

            <select name="sort_by">

                <option
                    value="id"
                    {{ $sortBy === 'id' ? 'selected' : '' }}>
                    ID
                </option>

                <option
                    value="name"
                    {{ $sortBy === 'name' ? 'selected' : '' }}>
                    Name
                </option>

                <option
                    value="email"
                    {{ $sortBy === 'email' ? 'selected' : '' }}>
                    Email
                </option>

                <option
                    value="created_at"
                    {{ $sortBy === 'created_at' ? 'selected' : '' }}>
                    Created Date
                </option>

            </select>


            <select name="sort_order">

                <option
                    value="asc"
                    {{ $sortOrder === 'asc' ? 'selected' : '' }}>
                    ASC
                </option>

                <option
                    value="desc"
                    {{ $sortOrder === 'desc' ? 'selected' : '' }}>
                    DESC
                </option>

            </select>


            <button
                type="submit"
                class="blue">
                🔎 Apply
            </button>


            <a
                href="{{ route('students.index') }}"
                class="gray btn">
                Reset
            </a>

        </form>


        {{-- ================================================= --}}
        {{-- VIEW BUTTONS --}}
        {{-- ================================================= --}}

        <div class="top-actions">

            <a
                href="{{ route('students.index') }}"
                class="green btn">
                👨‍🎓 Active Students
            </a>


            <a
                href="{{ route('students.index', ['view' => 'trash']) }}"
                class="red btn">
                🗑️ Trash ({{ $totalTrash }})
            </a>


            <a
                href="{{ route('students.csv', request()->query()) }}"
                class="green btn">
                ⬇ Export CSV
            </a>


            <a
                href="{{ route('students.pdf') }}"
                class="red btn">
                📄 Export PDF
            </a>

        </div>


        {{-- ================================================= --}}
        {{-- STATISTICS --}}
        {{-- ================================================= --}}

        <div class="stats">

            <div class="stat-card">

                <h4>Total Students</h4>

                <p>
                    {{ $totalStudents }}
                </p>

            </div>


            <div class="stat-card">

                <h4>Trash</h4>

                <p>
                    {{ $totalTrash }}
                </p>

            </div>


            <div class="stat-card">

                <h4>Added Today</h4>

                <p>
                    {{ $todayStudents }}
                </p>

            </div>


            <div class="stat-card">

                <h4>This Week</h4>

                <p>
                    {{ $thisWeekStudents }}
                </p>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- STUDENT TABLE --}}
        {{-- ================================================= --}}

        <h3>

            @if($view === 'trash')

            🗑️ Trash Students

            @else

            👨‍🎓 Students

            @endif

        </h3>


        @if($view !== 'trash')

        <form
            method="POST"
            action="{{ route('students.bulk-delete') }}"
            id="bulkDeleteForm">

            @csrf

            @endif


            <table>

                <tr>

                    @if($view !== 'trash')
                    <th>
                        <input
                            type="checkbox"
                            id="selectAll">
                    </th>
                    @endif

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Created At</th>

                    <th>Action</th>

                </tr>


                @forelse($students as $s)

                <tr>

                    @if($view !== 'trash')

                    <td>

                        <input
                            type="checkbox"
                            name="student_ids[]"
                            value="{{ $s->id }}"
                            class="student-checkbox">

                    </td>

                    @endif


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
                        {{ $s->created_at->format('d M Y, h:i A') }}
                    </td>


                    <td>

                        @if($view === 'trash')

                        <form
                            method="POST"
                            action="{{ route('students.restore', $s->id) }}"
                            class="action-form">

                            @csrf

                            <button
                                type="submit"
                                class="green small-btn">
                                ♻ Restore
                            </button>

                        </form>

                        @else

                        <form
                            method="POST"
                            action="{{ route('students.delete', $s->id) }}"
                            class="action-form">

                            @csrf

                            @method('DELETE')

                            <button
                                type="submit"
                                class="red small-btn"
                                onclick="return confirm('Move this student to trash?')">
                                🗑 Delete
                            </button>

                        </form>

                        @endif

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="{{ $view === 'trash' ? 5 : 6 }}">
                        No students found.
                    </td>

                </tr>

                @endforelse

            </table>


            @if($view !== 'trash')

            <br>

            <button
                type="submit"
                class="red"
                onclick="return confirm('Move selected students to trash?')">
                🗑️ Bulk Delete Selected
            </button>

        </form>

        @endif


        {{-- ================================================= --}}
        {{-- PAGINATION --}}
        {{-- ================================================= --}}

        <div class="pagination">

            {{ $students->links() }}

        </div>


        {{-- ================================================= --}}
        {{-- IMPORT HISTORY --}}
        {{-- ================================================= --}}

        <h3>📜 CSV Import History</h3>


        <table>

            <tr>

                <th>ID</th>

                <th>File Name</th>

                <th>Total</th>

                <th>Imported</th>

                <th>Duplicates</th>

                <th>Failed</th>

                <th>Status</th>

                <th>Date</th>

                <th>Details</th>

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
                    {{ $history->status }}
                </td>

                <td>
                    {{ $history->created_at->format('d M Y, h:i A') }}
                </td>

                <td>

                    <a
                        href="/api/students/import-history/{{ $history->id }}"
                        target="_blank"
                        class="purple btn small-btn">
                        👁 View
                    </a>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="9">
                    No import history available.
                </td>

            </tr>

            @endforelse

        </table>

    </div>


    <script>
        // ================================================
        // SELECT ALL
        // ================================================

        const selectAll =
            document.getElementById('selectAll');

        if (selectAll) {

            selectAll.addEventListener(
                'change',
                function() {

                    document
                        .querySelectorAll('.student-checkbox')
                        .forEach(function(checkbox) {

                            checkbox.checked =
                                selectAll.checked;

                        });

                }
            );

        }
    </script>

</body>

</html>