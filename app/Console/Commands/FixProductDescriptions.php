<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class FixProductDescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-descriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean chat artifacts and auto-balance unclosed HTML tags across all product descriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting product description audit and cleanup...');

        $fixedCount = 0;
        $total = Product::count();
        $bar = $this->output->createProgressBar($total);

        foreach (Product::cursor() as $product) {
            $bar->advance();
            if (!$product->description) {
                continue;
            }

            $desc = $product->description;
            $opens = substr_count($desc, '<div');
            $closes = substr_count($desc, '</div>');
            $hasArtifacts = str_contains($desc, 'font-claude-response') 
                         || str_contains($desc, 'w-screen') 
                         || str_contains($desc, 'composer-parent')
                         || str_contains($desc, 'data-test-render-count');

            if ($opens !== $closes || $hasArtifacts) {
                // Strip rogue chat UI elements
                $desc = preg_replace('/<div[^>]*data-test-render-count.*?<div class=\"contents\">/is', '', $desc);
                $desc = preg_replace('/<div[^>]*font-claude-response.*?<div class=\"standard-markdown[^\"]*\">/is', '', $desc);
                $desc = preg_replace('/<li class=\"font-claude-response-body[^\"]*\">/is', '<li>', $desc);
                $desc = preg_replace('/<div[^>]*class=[\"\'][^\"]*(?:composer-parent|qMYqUG_|flex h-svh w-screen|group\/thread)[^\"]*[\"\'][^>]*>/is', '', $desc);

                // Auto-balance all HTML tags
                $doc = new \DOMDocument();
                libxml_use_internal_errors(true);
                $doc->loadHTML('<?xml encoding="utf-8" ?><body>' . $desc . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();

                $body = $doc->getElementsByTagName('body')->item(0);
                if ($body) {
                    $clean = '';
                    foreach ($body->childNodes as $node) {
                        $clean .= $doc->saveHTML($node);
                    }

                    if (!empty($clean)) {
                        $product->description = $clean;
                        $product->save();
                        $fixedCount++;
                    }
                }
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully audited {$total} products and fixed {$fixedCount} descriptions!");

        return 0;
    }
}
