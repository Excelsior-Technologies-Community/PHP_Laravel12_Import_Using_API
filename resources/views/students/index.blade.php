<!DOCTYPE html>
<html>

<head>

    <title>Student Data Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

        .student-form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 15px 0;
        }

        label { display: grid; gap: 5px; color: #475569; font-size: 13px; }
        textarea { resize: vertical; padding: 9px; border: 1px solid #ccc; border-radius: 4px; font: inherit; }
        .wide { grid-column: span 3; }
        .suggestion-wrap { position: relative; }
        .suggestions { position: absolute; z-index: 5; width: 100%; background: white; border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 4px 12px #0002; }
        .suggestions a { display: block; padding: 8px 10px; color: #334155; text-decoration: none; }
        .suggestions a:hover { background: #f1f5f9; }
        .toast { position: fixed; right: 22px; top: 22px; z-index: 20; padding: 12px 18px; border-radius: 5px; color: white; background: #16a34a; box-shadow: 0 4px 14px #0003; }
        .toast.error, .error-message { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 5px; }
        .loading { opacity: .6; pointer-events: none; }
        .profile-photo { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; }
        .details { display: grid; grid-template-columns: 160px 1fr; gap: 10px; margin: 20px 0; }
        .details dt { font-weight: bold; color: #64748b; }
        .details dd { margin: 0; }

        .small-btn {
            padding: 6px 9px;
            font-size: 12px;
        }

        @media(max-width: 800px) {

            .student-form-grid { grid-template-columns: repeat(2, 1fr); }
            .wide { grid-column: span 2; }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            table {
                font-size: 13px;
            }
        }

        @media(max-width: 500px) {

            .student-form-grid { grid-template-columns: 1fr; }
            .wide { grid-column: span 1; }

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

        <div id="toast" class="toast" hidden></div>


        {{-- ================================================= --}}
        {{-- SUCCESS MESSAGE --}}
        {{-- ================================================= --}}

        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif


        <div class="top-actions">
            <a href="{{ route('students.create') }}" class="blue btn">➕ Add Student</a>
        </div>


        {{-- ================================================= --}}
        {{-- FILTERS --}}
        {{-- ================================================= --}}

        <form
            method="GET"
            action="{{ route('students.index') }}"
            class="filters">

            <div class="suggestion-wrap">
                <input id="student-search" type="text" name="search" value="{{ $search }}" autocomplete="off" placeholder="🔎 Search name/email">
                <div id="suggestions" class="suggestions" hidden></div>
            </div>

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

            <form method="POST" action="{{ url('/api/students/import-csv') }}" class="ajax-form" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".csv,.txt" required>
                <button type="submit" class="purple">⬆ Import CSV</button>
            </form>

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
            id="bulkDeleteForm"
            class="ajax-form">

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
                    @else
                    <th>Select</th>
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

                    @else

                    <td><input type="checkbox" class="trash-checkbox" value="{{ $s->id }}"></td>

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

                        <a href="{{ route('students.show', $s->id) }}" class="blue btn small-btn">View</a>

                        @if($view !== 'trash')
                            <a href="{{ route('students.edit', $s->id) }}" class="orange btn small-btn">✏ Edit</a>
                        @endif

                        @if($view === 'trash')

                        <form
                            method="POST"
                            action="{{ route('students.restore', $s->id) }}"
                            class="action-form ajax-form">

                            @csrf

                            <button
                                type="submit"
                                class="green small-btn">
                                ♻ Restore
                            </button>

                        </form>

                        <form method="POST" action="{{ route('students.force-delete', $s->id) }}" class="action-form ajax-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="red small-btn" data-confirm="Permanently delete this student?">Delete Forever</button>
                        </form>

                        @else

                        <form
                            method="POST"
                            action="{{ route('students.delete', $s->id) }}"
                            class="action-form ajax-form">

                            @csrf

                            @method('DELETE')

                            <button type="submit" class="red small-btn" data-confirm="Move this student to trash?">
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
                data-confirm="Move selected students to trash?">
                🗑️ Bulk Delete Selected
            </button>

        </form>

        @endif

        @if($view === 'trash')
            <form method="POST" action="{{ route('students.bulk-restore') }}" id="bulkRestoreForm" class="action-form">
                @csrf
                <div id="restore-ids"></div>
                <button type="submit" class="green" data-confirm="Restore selected students?">♻ Bulk Restore</button>
            </form>
            <form method="POST" action="{{ route('students.bulk-force-delete') }}" id="bulkForceDeleteForm" class="action-form">
                @csrf
                <div id="force-delete-ids"></div>
                <button type="submit" class="red" data-confirm="Permanently delete selected students?">Delete Selected Forever</button>
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
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const toast = document.getElementById('toast');

        function showToast(message, error = false) {
            toast.textContent = message;
            toast.classList.toggle('error', error);
            toast.hidden = false;
            setTimeout(() => { toast.hidden = true; }, 3500);
        }

        document.querySelectorAll('[data-confirm]').forEach(button => {
            button.addEventListener('click', event => {
                if (!confirm(button.dataset.confirm)) event.preventDefault();
            });
        });

        document.getElementById('selectAll')?.addEventListener('change', event => {
            document.querySelectorAll('.student-checkbox').forEach(checkbox => {
                checkbox.checked = event.target.checked;
            });
        });

        function addSelectedIds(formId, targetId) {
            const target = document.getElementById(targetId);
            if (!target) return;
            target.innerHTML = '';
            document.querySelectorAll('.trash-checkbox:checked').forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_ids[]';
                input.value = checkbox.value;
                target.appendChild(input);
            });
        }

        document.getElementById('bulkRestoreForm')?.addEventListener('submit', () => addSelectedIds('bulkRestoreForm', 'restore-ids'));
        document.getElementById('bulkForceDeleteForm')?.addEventListener('submit', () => addSelectedIds('bulkForceDeleteForm', 'force-delete-ids'));

        document.querySelectorAll('.ajax-form').forEach(form => {
            form.addEventListener('submit', async event => {
                event.preventDefault();
                if (form.querySelector('[data-confirm]') && !confirm(form.querySelector('[data-confirm]').dataset.confirm)) return;
                form.classList.add('loading');
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: new FormData(form)
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Request failed.');
                    showToast(data.message || 'Done.');
                    setTimeout(() => window.location.reload(), 600);
                } catch (error) {
                    showToast(error.message, true);
                    form.classList.remove('loading');
                }
            });
        });

        const search = document.getElementById('student-search');
        const suggestions = document.getElementById('suggestions');
        let searchTimer;
        search?.addEventListener('input', () => {
            clearTimeout(searchTimer);
            const value = search.value.trim();
            if (!value) { suggestions.hidden = true; return; }
            searchTimer = setTimeout(async () => {
                const response = await fetch(`{{ route('students.search-suggestions') }}?search=${encodeURIComponent(value)}`);
                const students = await response.json();
                suggestions.innerHTML = students.map(student => `<a href="{{ url('/students') }}/${student.id}">${student.name} <small>${student.email}</small></a>`).join('');
                suggestions.hidden = students.length === 0;
            }, 250);
        });
        document.addEventListener('click', event => {
            if (!event.target.closest('.suggestion-wrap')) suggestions.hidden = true;
        });
    </script>

</body>

</html>