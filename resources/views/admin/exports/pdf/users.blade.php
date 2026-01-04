<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #22c55e;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #22c55e;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #22c55e;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CA Global Users Report</h1>
        <p>Staff Management System</p>
        <p><strong>Generated:</strong> {{ $generated_at }}</p>
    </div>

    <div class="summary">
        <h3 style="margin-top: 0; color: #22c55e;">Report Summary</h3>
        <p><strong>Total Users:</strong> {{ $data->count() }}</p>
        <p><strong>Active:</strong> {{ $data->where('status', 'active')->count() }} |
           <strong>Inactive:</strong> {{ $data->where('status', 'inactive')->count() }}</p>
        <p><strong>Instructors:</strong> {{ $data->where('user_type', 'instructor')->count() }} |
           <strong>Office Staff:</strong> {{ $data->where('user_type', 'office_staff')->count() }} |
           <strong>Admins:</strong> {{ $data->where('user_type', 'admin')->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $user)
            <tr>
                <td>{{ $user->employee_no ?? '-' }}</td>
                <td><strong>{{ $user->name ?? '-' }}</strong></td>
                <td>{{ $user->email ?? '-' }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>{{ $user->department ?? '-' }}</td>
                <td>{{ ucfirst($user->user_type ?? '-') }}</td>
                <td>
                    <span class="status-badge status-{{ $user->status }}">
                        {{ strtoupper($user->status ?? 'N/A') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>CA Global - Staff Management System</p>
        <p>This is a computer-generated report. No signature required.</p>
    </div>
</body>
</html>
