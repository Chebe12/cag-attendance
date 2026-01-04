<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * UserExport
 *
 * Exports user records to Excel format
 * with proper formatting and column headers
 */
class UserExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $users;

    /**
     * Constructor
     *
     * @param \Illuminate\Support\Collection $users
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Return the collection to be exported
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->users;
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
            'First Name',
            'Middle Name',
            'Last Name',
            'Email',
            'Phone',
            'Department',
            'User Type',
            'Status',
            'Date Joined',
        ];
    }

    /**
     * Map data for each row
     *
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->employee_no ?? '-',
            $user->firstname ?? '-',
            $user->middlename ?? '-',
            $user->lastname ?? '-',
            $user->email ?? '-',
            $user->phone ?? '-',
            $user->department ?? '-',
            ucfirst($user->user_type ?? '-'),
            ucfirst($user->status ?? '-'),
            $user->created_at ? $user->created_at->format('Y-m-d') : '-',
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
            'B' => 20,  // First Name
            'C' => 20,  // Middle Name
            'D' => 20,  // Last Name
            'E' => 25,  // Email
            'F' => 15,  // Phone
            'G' => 20,  // Department
            'H' => 15,  // User Type
            'I' => 12,  // Status
            'J' => 15,  // Date Joined
        ];
    }
}
