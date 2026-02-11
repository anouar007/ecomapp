<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return the headings for the Excel file.
     */
    public function headings(): array
    {
        return [
            'name',
            'sku',
            'description',
            'price',
            'cost_price',
            'stock',
            'min_stock',
            'category',
            'status',
        ];
    }

    /**
     * Return sample data rows.
     */
    public function array(): array
    {
        return [
            ['Sample Product 1', 'SKU-001', 'Product description here', 99.99, 50.00, 100, 10, 'General', 'active'],
            ['Sample Product 2', 'SKU-002', 'Another product example', 149.99, 75.00, 50, 5, 'General', 'active'],
            ['Sample Product 3', 'SKU-003', 'Third sample product', 199.99, 100.00, 25, 3, 'Electronics', 'active'],
        ];
    }

    /**
     * Define column widths for better readability.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // name
            'B' => 15, // sku
            'C' => 35, // description
            'D' => 12, // price
            'E' => 12, // cost_price
            'F' => 10, // stock
            'G' => 12, // min_stock
            'H' => 15, // category
            'I' => 12, // status
        ];
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
