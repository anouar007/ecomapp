<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Str;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable, SkipsErrors;

    private $categoriesCache = [];
    private $importedCount = 0;

    public function __construct()
    {
        // Cache categories for lookup by name
        $this->categoriesCache = Category::pluck('id', 'name')->toArray();
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['name'])) {
            return null;
        }

        // Find category by name (case-insensitive)
        $categoryId = null;
        if (!empty($row['category'])) {
            $categoryName = trim($row['category']);
            foreach ($this->categoriesCache as $name => $id) {
                if (strcasecmp($name, $categoryName) === 0) {
                    $categoryId = $id;
                    break;
                }
            }
        }

        // Generate SKU if not provided
        $sku = !empty($row['sku']) ? $row['sku'] : 'SKU-' . strtoupper(Str::random(8));
        
        // Check if product with this SKU already exists
        $existingProduct = Product::where('sku', $sku)->first();
        if ($existingProduct) {
            // Update existing product
            $existingProduct->update([
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
                'price' => $row['price'] ?? 0,
                'cost_price' => $row['cost_price'] ?? null,
                'stock' => $row['stock'] ?? 0,
                'min_stock' => $row['min_stock'] ?? 5,
                'category_id' => $categoryId,
                'status' => $this->normalizeStatus($row['status'] ?? 'active'),
            ]);
            $this->importedCount++;
            return null; // Don't create new, we updated
        }

        $this->importedCount++;

        return new Product([
            'name' => $row['name'],
            'sku' => $sku,
            'description' => $row['description'] ?? null,
            'price' => $row['price'] ?? 0,
            'cost_price' => $row['cost_price'] ?? null,
            'stock' => $row['stock'] ?? 0,
            'min_stock' => $row['min_stock'] ?? 5,
            'category_id' => $categoryId,
            'status' => $this->normalizeStatus($row['status'] ?? 'active'),
        ]);
    }

    /**
     * Normalize status value
     */
    private function normalizeStatus($status): string
    {
        $status = strtolower(trim($status));
        return in_array($status, ['active', 'inactive']) ? $status : 'active';
    }

    /**
     * Validation rules for each row
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'category' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive,Active,Inactive',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'price.required' => 'Product price is required',
            'price.numeric' => 'Product price must be a number',
        ];
    }

    /**
     * Get the count of imported products
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
