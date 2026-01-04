<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * ClientExport
 *
 * Exports client records to Excel format
 * with proper formatting and column headers
 */
class ClientExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $clients;

    /**
     * Constructor
     *
     * @param \Illuminate\Support\Collection $clients
     */
    public function __construct($clients)
    {
        $this->clients = $clients;
    }

    /**
     * Return the collection to be exported
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->clients;
    }

    /**
     * Define column headings
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Address',
            'City',
            'State',
            'Country',
            'Status',
            'Contact Person',
            'Contact Phone',
            'Created Date',
        ];
    }

    /**
     * Map data for each row
     *
     * @param mixed $client
     * @return array
     */
    public function map($client): array
    {
        return [
            $client->name ?? '-',
            $client->email ?? '-',
            $client->phone ?? '-',
            $client->address ?? '-',
            $client->city ?? '-',
            $client->state ?? '-',
            $client->country ?? '-',
            ucfirst($client->status ?? '-'),
            $client->contact_person ?? '-',
            $client->contact_phone ?? '-',
            $client->created_at ? $client->created_at->format('Y-m-d') : '-',
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
            'A' => 30,  // Name
            'B' => 25,  // Email
            'C' => 15,  // Phone
            'D' => 35,  // Address
            'E' => 15,  // City
            'F' => 15,  // State
            'G' => 15,  // Country
            'H' => 12,  // Status
            'I' => 25,  // Contact Person
            'J' => 15,  // Contact Phone
            'K' => 15,  // Created Date
        ];
    }
}
