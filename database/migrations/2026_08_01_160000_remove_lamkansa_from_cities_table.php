<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('cities')
            ->where('arabic_name', 'like', '%لمكانسة%')
            ->orWhere('name', 'like', '%Lamkansa%')
            ->orWhere('name', 'like', '%Lmkansa%')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
