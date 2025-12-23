<!DOCTYPE html>
<html>
<head>
    <title>Student Data</title>

    <style>
        /* Page base styling */
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 30px;
        }

        /* Main container box */
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* Page heading */
        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        /* Form layout */
        form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        /* Input fields */
        input {
            padding: 8px;
            width: 30%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Default button style */
        button {
            padding: 8px 14px;
            background: #2563eb;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Button hover effect */
        button:hover {
            background: #1e40af;
        }

        /* Export buttons wrapper */
        .actions {
            margin-bottom: 15px;
        }

        /* Export buttons */
        .actions a {
            text-decoration: none;
            padding: 6px 12px;
            margin-right: 5px;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
        }

        /* CSV button */
        .csv {
            background: #16a34a;
        }

        /* PDF button */
        .pdf {
            background: #dc2626;
        }

        /* Table base styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Table cells */
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        /* Table header */
        table th {
            background: #f1f5f9;
        }

        /* Zebra rows */
        table tr:nth-child(even) {
            background: #f9fafb;
        }

        /* Delete button */
        .delete-btn {
            background: #dc2626;
            padding: 6px 10px;
            font-size: 13px;
        }

        /* Delete hover */
        .delete-btn:hover {
            background: #991b1b;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Page title -->
    <h2>🎓 Student Data</h2>

    <!-- Create student form -->
    <form method="POST" action="{{ route('students.store') }}">
        @csrf <!-- CSRF protection -->
        <input name="name" placeholder="Name" required>
        <input name="email" placeholder="Email" required>
        <button>Add</button>
    </form>

    <!-- Export buttons -->
    <div class="actions">
        <a href="{{ route('students.csv') }}" class="csv">⬇ Export CSV</a>
        <a href="{{ route('students.pdf') }}" class="pdf">📄 Export PDF</a>
    </div>

    <!-- Students table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>

        <!-- Loop through students -->
        @foreach($students as $s)
        <tr>
            <td>{{ $s->id }}</td>
            <td>{{ $s->name }}</td>
            <td>{{ $s->email }}</td>
            <td>
                <!-- Delete student -->
                <form method="POST" action="{{ route('students.delete',$s->id) }}">
                    @csrf
                    @method('DELETE') <!-- Method spoofing -->
                    <button class="delete-btn"
                        onclick="return confirm('Delete this student?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        @endforeach

    </table>

</div>

</body>
</html>
