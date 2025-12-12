<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * SchedulesExport
 *
 * Exports schedule records to Excel format
 * with proper formatting and column headers
 */
class SchedulesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $schedules;

    /**
     * Constructor
     *
     * @param \Illuminate\Support\Collection $schedules
     */
    public function __construct($schedules)
    {
        $this->schedules = $schedules;
    }

    /**
     * Return the collection to be exported
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->schedules;
    }

    /**
     * Define column headings
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Employee No',
            'Instructor',
            'Client',
            'Scheduled Date',
            'Start Time',
            'End Time',
            'Shift',
            'Status',
            'Attendance Status',
            'Check In',
            'Check Out',
            'Created By',
            'Notes',
        ];
    }

    /**
     * Map data for each row
     *
     * @param mixed $schedule
     * @return array
     */
    public function map($schedule): array
    {
        $startTime = $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : '-';
        $endTime = $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : '-';

        $attendanceStatus = '-';
        $checkIn = '-';
        $checkOut = '-';

        // Note: Uses singular attendance() relationship for first/latest attendance record
        // Multiple attendance records can exist via attendances() relationship
        if ($schedule->attendance) {
            $attendanceStatus = ucfirst($schedule->attendance->status);
            $checkIn = $schedule->attendance->check_in ?
                \Carbon\Carbon::parse($schedule->attendance->check_in)->format('h:i A') : '-';
            $checkOut = $schedule->attendance->check_out ?
                \Carbon\Carbon::parse($schedule->attendance->check_out)->format('h:i A') : '-';
        }

        return [
            $schedule->id,
            optional($schedule->user)->employee_no ?? '-',
            optional($schedule->user)->full_name ?? '-',
            optional($schedule->client)->name ?? '-',
            \Carbon\Carbon::parse($schedule->scheduled_date)->format('Y-m-d'),
            $startTime,
            $endTime,
            optional($schedule->shift)->name ?? '-',
            ucfirst($schedule->status),
            $attendanceStatus,
            $checkIn,
            $checkOut,
            optional($schedule->creator)->full_name ?? '-',
            $schedule->notes ?? '-',
        ];
    }

    /**
     * Apply styles to the worksheet
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22C55E'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Define column widths
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 15,  // Employee No
            'C' => 25,  // Instructor
            'D' => 30,  // Client
            'E' => 15,  // Scheduled Date
            'F' => 12,  // Start Time
            'G' => 12,  // End Time
            'H' => 15,  // Shift
            'I' => 12,  // Status
            'J' => 18,  // Attendance Status
            'K' => 12,  // Check In
            'L' => 12,  // Check Out
            'M' => 25,  // Created By
            'N' => 30,  // Notes
        ];
    }
}
