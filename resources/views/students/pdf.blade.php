<!DOCTYPE html>
<html>
<head>
    <title>Students List</title>

    <style>
        /* PDF safe font */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        /* PDF heading */
        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Table header cells */
        table th {
            background-color: #f1f5f9;
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        /* Table body cells */
        table td {
            border: 1px solid #000;
            padding: 8px;
        }

        /* Zebra rows */
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
    </style>
</head>

<body>

<!-- PDF title -->
<h2>Students List</h2>

<!-- PDF data table -->
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
    </tr>

    <!-- Loop through students -->
    @foreach($students as $s)
    <tr>
        <td>{{ $s->id }}</td>
        <td>{{ $s->name }}</td>
        <td>{{ $s->email }}</td>
    </tr>
    @endforeach
</table>

</body>
</html>
