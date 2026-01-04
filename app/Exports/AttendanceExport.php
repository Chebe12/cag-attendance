<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * AttendanceExport
 *
 * Exports attendance records to Excel format
 * with proper formatting and column headers
 */
class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $attendances;

    /**
     * Constructor
     *
     * @param \Illuminate\Support\Collection $attendances
     */
    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    /**
     * Return the collection to be exported
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->attendances;
    }

    /**
     * Define column headings
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Employee No',
            'Name',
            'Department',
            'Date',
            'Check In',
            'Check Out',
            'Status',
            'Work Duration (Hours)',
            'Client',
            'Shift',
            'Location',
            'IP Address',
            'Notes',
        ];
    }

    /**
     * Map data for each row
     *
     * @param mixed $attendance
     * @return array
     */
    public function map($attendance): array
    {
        $checkIn = $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-';
        $checkOut = $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-';
        $workDuration = $attendance->work_duration ? round($attendance->work_duration / 60, 2) : 0;

        return [
            $attendance->user->employee_no ?? '-',
            $attendance->user->name ?? '-',
            $attendance->user->department ?? '-',
            \Carbon\Carbon::parse($attendance->attendance_date)->format('Y-m-d'),
            $checkIn,
            $checkOut,
            ucfirst($attendance->status),
            $workDuration,
            $attendance->client->name ?? '-',
            $attendance->shift->name ?? '-',
            $attendance->check_in_location ?? '-',
            $attendance->check_in_ip ?? '-',
            $attendance->notes ?? '-',
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
            'A' => 15,  // Employee No
            'B' => 25,  // Name
            'C' => 20,  // Department
            'D' => 12,  // Date
            'E' => 12,  // Check In
            'F' => 12,  // Check Out
            'G' => 12,  // Status
            'H' => 20,  // Work Duration
            'I' => 30,  // Client
            'J' => 15,  // Shift
            'K' => 20,  // Location
            'L' => 15,  // IP Address
            'M' => 30,  // Notes
        ];
    }
}
