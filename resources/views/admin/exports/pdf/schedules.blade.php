<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Schedules Report</title>
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
        .status-scheduled {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-cancelled {
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
        <h1>CA Global Schedules Report</h1>
        <p>Schedule Management System</p>
        <p><strong>Generated:</strong> {{ $generated_at }}</p>
    </div>

    <div class="summary">
        <h3 style="margin-top: 0; color: #22c55e;">Report Summary</h3>
        <p><strong>Total Schedules:</strong> {{ $data->count() }}</p>
        <p><strong>Scheduled:</strong> {{ $data->where('status', 'scheduled')->count() }} |
           <strong>Completed:</strong> {{ $data->where('status', 'completed')->count() }} |
           <strong>Cancelled:</strong> {{ $data->where('status', 'cancelled')->count() }}</p>
        <p><strong>Published:</strong> {{ $data->where('draft_status', 'published')->count() }} |
           <strong>Drafts:</strong> {{ $data->where('draft_status', 'draft')->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Instructor</th>
                <th>Client</th>
                <th>Day</th>
                <th>Session</th>
                <th>Time</th>
                <th>Shift</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $schedule)
            <tr>
                <td>
                    <strong>{{ $schedule->user->name ?? '-' }}</strong><br>
                    <small>{{ $schedule->user->employee_no ?? '-' }}</small>
                </td>
                <td>{{ $schedule->client->name ?? '-' }}</td>
                <td>{{ ucfirst($schedule->day_of_week ?? '-') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $schedule->session_time ?? '-')) }}</td>
                <td>
                    {{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : '-' }} -
                    {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : '-' }}
                </td>
                <td>{{ $schedule->shift->name ?? '-' }}</td>
                <td>
                    <span class="status-badge status-{{ $schedule->status }}">
                        {{ strtoupper($schedule->status ?? 'N/A') }}
                    </span>
                    @if($schedule->draft_status === 'draft')
                    <br><small style="color: #f59e0b;">DRAFT</small>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>CA Global - Schedule Management System</p>
        <p>This is a computer-generated report. No signature required.</p>
    </div>
</body>
</html>
