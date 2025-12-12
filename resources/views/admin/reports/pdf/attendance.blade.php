<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
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
        .info {
            margin-bottom: 20px;
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
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-present {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-late {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-absent {
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
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CA Global Attendance Report</h1>
        <p>Attendance Management System</p>
        <p><strong>Generated:</strong> {{ $generated_at }}</p>
    </div>

    <div class="summary">
        <h3 style="margin-top: 0; color: #22c55e;">Report Summary</h3>
        <p><strong>Total Records:</strong> {{ $data->count() }}</p>
        <p><strong>Present:</strong> {{ $data->where('status', 'present')->count() }} |
           <strong>Late:</strong> {{ $data->where('status', 'late')->count() }} |
           <strong>Absent:</strong> {{ $data->where('status', 'absent')->count() }}</p>
        <p><strong>Total Work Hours:</strong> {{ round($data->sum('work_duration') / 60, 2) }} hours</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Hours</th>
                <th>Client</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $attendance)
            <tr>
                <td>
                    <strong>{{ $attendance->user->full_name ?? '-' }}</strong><br>
                    <small>{{ $attendance->user->employee_no ?? '-' }}</small>
                </td>
                <td>{{ $attendance->user->department ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</td>
                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                <td>
                    @if($attendance->status == 'present')
                        <span class="status status-present">PRESENT</span>
                    @elseif($attendance->status == 'late')
                        <span class="status status-late">LATE</span>
                    @else
                        <span class="status status-absent">ABSENT</span>
                    @endif
                </td>
                <td>{{ $attendance->work_duration ? round($attendance->work_duration / 60, 2) : '0.00' }} hrs</td>
                <td>{{ $attendance->schedule->client->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>CA Global - Attendance Management System</p>
        <p>This is a computer-generated report. No signature required.</p>
    </div>
</body>
</html>
