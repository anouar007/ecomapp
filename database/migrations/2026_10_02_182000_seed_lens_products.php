<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Product seeding/mutation permanently disabled to protect live catalog data.
    }

    public function down(): void
    {
        // No-op.
    }
};
