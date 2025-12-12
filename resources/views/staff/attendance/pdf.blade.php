<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Attendance Report</title>
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
        .employee-info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .employee-info h3 {
            margin-top: 0;
            color: #22c55e;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 5px;
            font-weight: bold;
            width: 30%;
        }
        .info-value {
            display: table-cell;
            padding: 5px;
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
        .status-on_leave {
            background-color: #dbeafe;
            color: #1e40af;
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
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            padding: 5px;
            text-align: center;
            border-right: 1px solid #ddd;
        }
        .summary-cell:last-child {
            border-right: none;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #22c55e;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Personal Attendance Report</h1>
        <p>CA Global Attendance Management System</p>
        <p><strong>Generated:</strong> {{ $generated_at }}</p>
    </div>

    <div class="employee-info">
        <h3>Employee Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $user->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Employee No:</div>
                <div class="info-value">{{ $user->employee_no }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Department:</div>
                <div class="info-value">{{ $user->department ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">User Type:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</div>
            </div>
        </div>
    </div>

    <div class="summary">
        <h3 style="margin-top: 0; color: #22c55e;">Attendance Summary</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Total Days</div>
                    <div class="summary-value">{{ $data->count() }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Present</div>
                    <div class="summary-value" style="color: #059669;">{{ $data->where('status', 'present')->count() }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Late</div>
                    <div class="summary-value" style="color: #d97706;">{{ $data->where('status', 'late')->count() }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Absent</div>
                    <div class="summary-value" style="color: #dc2626;">{{ $data->where('status', 'absent')->count() }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Total Hours</div>
                    <div class="summary-value">{{ round($data->sum('work_duration') / 60, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Hours</th>
                <th>Client/Location</th>
                <th>Shift</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $attendance)
            <tr>
                <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y (D)') }}</td>
                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                <td>
                    @if($attendance->status == 'present')
                        <span class="status status-present">PRESENT</span>
                    @elseif($attendance->status == 'late')
                        <span class="status status-late">LATE</span>
                    @elseif($attendance->status == 'on_leave')
                        <span class="status status-on_leave">ON LEAVE</span>
                    @else
                        <span class="status status-absent">ABSENT</span>
                    @endif
                </td>
                <td>{{ $attendance->work_duration ? round($attendance->work_duration / 60, 2) : '0.00' }} hrs</td>
                <td>
                    @if($attendance->schedule && $attendance->schedule->client)
                        <strong>{{ $attendance->schedule->client->name }}</strong><br>
                        <small>{{ $attendance->schedule->client->location ?? '-' }}</small>
                    @else
                        <span style="color: #999;">Office</span>
                    @endif
                </td>
                <td>{{ $attendance->schedule->shift->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($data->isEmpty())
    <div style="text-align: center; padding: 40px; color: #999;">
        <p style="font-size: 16px;">No attendance records found for the selected period.</p>
    </div>
    @endif

    <div class="footer">
        <p>CA Global - Attendance Management System</p>
        <p>This is a computer-generated report. No signature required.</p>
        <p style="margin-top: 10px; font-size: 9px; color: #999;">
            This report contains confidential information intended only for {{ $user->full_name }}.
        </p>
    </div>
</body>
</html>
