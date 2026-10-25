<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class RemoveProductShowcases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:remove-showcases {id? : ID spécifique du produit (optionnel)} {--all : Supprimer les showcases de tous les produits}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprimer le bloc Showcase visuel de la description des produits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $all = $this->option('all');

        if (!$id && !$all) {
            $this->error("Veuillez spécifier l'ID d'un produit (ex: php artisan products:remove-showcases 887) ou utiliser --all.");
            return 1;
        }

        $query = Product::whereNotNull('description');
        if ($id) {
            $query->where('id', $id);
        }

        $products = $query->get();
        if ($products->isEmpty()) {
            $this->warn("Aucun produit trouvé.");
            return 0;
        }

        $count = 0;

        foreach ($products as $product) {
            $desc = $product->description;
            if (stripos($desc, 'pdp-visual-showcase') !== false || stripos($desc, 'pdp-showcase-card') !== false) {
                // Remove entire pdp-visual-showcase block including heading and cards
                $cleaned = preg_replace('/<div[^>]*class=[\"\'][^\"\']*pdp-visual-showcase[^\"\']*[\"\'][^>]*>.*?<\/div>\s*<\/div>/is', '', $desc);
                // Also clean any isolated showcase cards or grids if malformed
                $cleaned = preg_replace('/<div[^>]*class=[\"\'][^\"\']*pdp-showcase-grid[^\"\']*[\"\'][^>]*>.*?<\/div>/is', '', $cleaned);
                $cleaned = preg_replace('/<div[^>]*class=[\"\'][^\"\']*pdp-showcase-card[^\"\']*[\"\'][^>]*>.*?<\/div>/is', '', $cleaned);

                if ($cleaned !== $desc) {
                    $product->description = trim($cleaned);
                    $product->save();
                    $count++;
                    $this->info("Showcase supprimé pour : #{$product->id} - {$product->name}");
                }
            }
        }

        $this->info("Terminé ! {$count} produit(s) mis à jour.");
        return 0;
    }
}
