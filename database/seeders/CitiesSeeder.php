<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        // Read from the downloaded JSON file
        $jsonPath = '/Users/anouar/Downloads/cities.json';
        $json = json_decode(file_get_contents($jsonPath), true);

        // Find the table data block
        $rows = [];
        foreach ($json as $block) {
            if (isset($block['type']) && $block['type'] === 'table' && $block['name'] === 'cities') {
                $rows = $block['data'];
                break;
            }
        }

        if (empty($rows)) {
            $this->command->error('No cities data found in JSON.');
            return;
        }

        DB::table('cities')->truncate();

        $now = now();
        $insert = array_map(fn($r) => [
            'name'        => $r['name'],
            'arabic_name' => $r['arabic_name'],
            'price'       => (float) $r['price'],
            'created_at'  => $now,
            'updated_at'  => $now,
        ], $rows);

        // Insert in chunks to avoid query size limits
        foreach (array_chunk($insert, 100) as $chunk) {
            DB::table('cities')->insert($chunk);
        }

        $this->command->info('✅ ' . count($insert) . ' cities seeded successfully.');
    }
}
