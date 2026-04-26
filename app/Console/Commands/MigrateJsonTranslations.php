<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;

class MigrateJsonTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-json-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate raw JSON translations from name and description to specific language columns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting JSON translations migration...');

        $this->migrateCategories();
        $this->migrateProducts();

        $this->info('Migration completed successfully.');
    }

    private function migrateCategories()
    {
        $this->info('Migrating Categories...');
        $categories = Category::all();
        $count = 0;

        foreach ($categories as $category) {
            $updated = false;

            // Handle name
            if ($this->isJson($category->name)) {
                $nameData = json_decode($category->name, true);
                if (is_array($nameData)) {
                    $category->name_en = $nameData['en'] ?? null;
                    $category->name_fr = $nameData['fr'] ?? null;
                    $category->name_ar = $nameData['ar'] ?? null;
                    // Fallback to FR then EN then AR for the default name field
                    $category->name = $nameData['fr'] ?? $nameData['en'] ?? $nameData['ar'] ?? 'Unknown';
                    $updated = true;
                }
            }

            // Handle description
            if ($category->description && $this->isJson($category->description)) {
                $descData = json_decode($category->description, true);
                if (is_array($descData)) {
                    $category->description_en = $descData['en'] ?? null;
                    $category->description_fr = $descData['fr'] ?? null;
                    $category->description_ar = $descData['ar'] ?? null;
                    // Fallback to FR then EN then AR for the default description field
                    $category->description = $descData['fr'] ?? $descData['en'] ?? $descData['ar'] ?? null;
                    $updated = true;
                }
            }

            if ($updated) {
                // Ignore model events here to avoid changing slugs or timestamps unnecessarily if desired, 
                // but standard save is okay since it's an admin operation.
                $category->save();
                $count++;
            }
        }

        $this->info("Successfully migrated $count categories.");
    }

    private function migrateProducts()
    {
        $this->info('Migrating Products...');
        $products = Product::all();
        $count = 0;

        foreach ($products as $product) {
            $updated = false;

            // Handle name
            if ($this->isJson($product->name)) {
                $nameData = json_decode($product->name, true);
                if (is_array($nameData)) {
                    $product->name_en = $nameData['en'] ?? null;
                    $product->name_fr = $nameData['fr'] ?? null;
                    $product->name_ar = $nameData['ar'] ?? null;
                    // Fallback to FR then EN then AR for the default name field
                    $product->name = $nameData['fr'] ?? $nameData['en'] ?? $nameData['ar'] ?? 'Unknown';
                    $updated = true;
                }
            }

            // Handle description
            if ($product->description && $this->isJson($product->description)) {
                $descData = json_decode($product->description, true);
                if (is_array($descData)) {
                    $product->description_en = $descData['en'] ?? null;
                    $product->description_fr = $descData['fr'] ?? null;
                    $product->description_ar = $descData['ar'] ?? null;
                    // Fallback to FR then EN then AR for the default description field
                    $product->description = $descData['fr'] ?? $descData['en'] ?? $descData['ar'] ?? null;
                    $updated = true;
                }
            }

            if ($updated) {
                $product->save();
                $count++;
            }
        }

        $this->info("Successfully migrated $count products.");
    }

    /**
     * Helper to check if a string is valid JSON.
     */
    private function isJson($string)
    {
        if (!is_string($string)) return false;
        
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE && (str_starts_with(trim($string), '{') || str_starts_with(trim($string), '['));
    }
}
