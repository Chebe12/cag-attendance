<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * ScheduleExport
 *
 * Exports schedule records to Excel format
 * with proper formatting and column headers
 */
class ScheduleExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
            'Instructor',
            'Employee No',
            'Client',
            'Day of Week',
            'Session Time',
            'Start Time',
            'End Time',
            'Shift',
            'Status',
            'Draft Status',
            'Category',
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
        return [
            $schedule->user->name ?? '-',
            $schedule->user->employee_no ?? '-',
            $schedule->client->name ?? '-',
            ucfirst($schedule->day_of_week ?? '-'),
            ucfirst(str_replace('_', ' ', $schedule->session_time ?? '-')),
            $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : '-',
            $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : '-',
            $schedule->shift->name ?? '-',
            ucfirst($schedule->status ?? '-'),
            ucfirst($schedule->draft_status ?? '-'),
            $schedule->category->name ?? '-',
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
            'A' => 25,  // Instructor
            'B' => 15,  // Employee No
            'C' => 30,  // Client
            'D' => 15,  // Day of Week
            'E' => 15,  // Session Time
            'F' => 12,  // Start Time
            'G' => 12,  // End Time
            'H' => 15,  // Shift
            'I' => 12,  // Status
            'J' => 12,  // Draft Status
            'K' => 25,  // Category
            'L' => 30,  // Notes
        ];
    }
}
