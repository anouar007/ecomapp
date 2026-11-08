<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\ProductImage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Repair DJI Osmo Pocket 4 Standard Combo (ID 713 or matched by name)
        $pocket4 = Product::where('id', 713)
            ->orWhere('name', 'like', '%DJI Osmo Pocket 4 Standard Combo%')
            ->first();

        if ($pocket4) {
            ProductImage::where('product_id', $pocket4->id)
                ->where('image_path', 'like', '%1111111111111112222222%')
                ->update([
                    'image_path' => 'images/products/OsmoPocket4-1.webp',
                    'is_primary' => 1,
                ]);

            $pocket4->update(['image' => 'images/products/OsmoPocket4-1.webp']);
        }

        // 2. Repair non-Godox products that have dummy Godox softbox images as primary
        $wrongProducts = Product::where('image', 'like', '%Godox-Boite-a-lumiere%')
            ->orWhere('image', 'like', '%1111111111111112222222%')
            ->orWhereHas('images', function ($q) {
                $q->where('image_path', 'like', '%Godox-Boite-a-lumiere%')
                  ->orWhere('image_path', 'like', '%1111111111111112222222%');
            })->get();

        foreach ($wrongProducts as $p) {
            $pName = strtolower($p->name);
            $isGodoxLighting = str_contains($pName, 'godox') || 
                               str_contains($pName, 'boîte') || 
                               str_contains($pName, 'boite') || 
                               str_contains($pName, 'softbox') || 
                               str_contains($pName, 'ml-sf50');
            if ($isGodoxLighting) {
                continue;
            }

            // Find first real image that is not Godox or generic logo
            $validImg = $p->images()->where(function ($q) {
                $q->where('image_path', 'not like', '%Godox-Boite-a-lumiere%')
                  ->where('image_path', 'not like', '%1111111111111112222222%')
                  ->where('image_path', 'not like', '%winashop.jpg%');
            })->first();

            if ($validImg) {
                // Demote godox images on this product
                ProductImage::where('product_id', $p->id)
                    ->where(function ($q) {
                        $q->where('image_path', 'like', '%Godox-Boite-a-lumiere%')
                          ->orWhere('image_path', 'like', '%1111111111111112222222%');
                    })
                    ->update(['is_primary' => 0]);

                // Promote valid image to primary
                $validImg->update(['is_primary' => 1]);
                $p->update(['image' => $validImg->image_path]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op (data fix)
    }
};
