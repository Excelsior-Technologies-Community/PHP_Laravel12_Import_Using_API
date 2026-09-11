<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Management')</title>
    <style>
        :root { --blue: #2563eb; --ink: #1e293b; --muted: #64748b; --line: #e2e8f0; --surface: #fff; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 32px 16px; color: var(--ink); background: #f1f5f9; font-family: Arial, sans-serif; }
        .container { width: min(100%, 1100px); margin: auto; padding: 28px; background: var(--surface); border: 1px solid var(--line); border-radius: 10px; box-shadow: 0 8px 30px #0f172a12; }
        h1, h2, h3 { margin-top: 0; color: var(--ink); }
        .page-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
        .page-header p { margin: 5px 0 0; color: var(--muted); }
        .student-form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin: 20px 0; }
        label { display: grid; gap: 6px; color: #475569; font-size: 13px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px 11px; border: 1px solid #cbd5e1; border-radius: 5px; background: #fff; color: var(--ink); font: inherit; }
        textarea { resize: vertical; }
        .wide { grid-column: span 3; }
        button, .btn { display: inline-block; padding: 10px 14px; border: 0; border-radius: 5px; color: #fff; cursor: pointer; text-decoration: none; font-size: 14px; }
        .blue { background: var(--blue); } .green { background: #16a34a; } .red { background: #dc2626; } .gray { background: #64748b; } .purple { background: #7c3aed; }
        .success-message { margin-bottom: 18px; padding: 12px; border-radius: 5px; background: #dcfce7; color: #166534; }
        .error-message { margin-bottom: 18px; padding: 12px; border-radius: 5px; background: #fee2e2; color: #991b1b; }
        .profile-photo { width: 112px; height: 112px; border-radius: 50%; object-fit: cover; border: 4px solid #dbeafe; }
        .details { display: grid; grid-template-columns: 160px 1fr; gap: 12px; margin: 24px 0; }
        .details dt { color: var(--muted); font-weight: 700; } .details dd { margin: 0; }
        .form-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        @media (max-width: 700px) { .student-form-grid { grid-template-columns: repeat(2, 1fr); } .wide { grid-column: span 2; } .page-header { align-items: flex-start; flex-direction: column; } }
        @media (max-width: 480px) { body { padding: 16px 8px; } .container { padding: 20px 16px; } .student-form-grid { grid-template-columns: 1fr; } .wide { grid-column: span 1; } .details { grid-template-columns: 1fr; gap: 4px; } }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>