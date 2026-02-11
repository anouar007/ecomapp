<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ProductExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    /**
     * @return array
     */
    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::with('category')->get();
    }

    /**
     * Map the data for each row.
     */
    public function map($product): array
    {
        return [
            $product->name,
            $product->sku,
            $product->description,
            $product->price,
            $product->cost_price,
            $product->stock,
            $product->min_stock,
            $product->category->name ?? '',
            $product->status,
        ];
    }

    /**
     * Return the headings for the file.
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
}
