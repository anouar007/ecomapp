<?php

namespace App\Console\Commands;

use App\Models\RecurringExpense;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringExpensesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expenses:generate-recurring {--month= : Target month in YYYY-MM format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically generate due recurring expenses for the current or specified month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = $this->option('month')
            ? Carbon::parse($this->option('month') . '-01')
            : Carbon::now();

        $this->info("Scanning recurring expenses for {$targetDate->translatedFormat('F Y')}...");

        $recurring = RecurringExpense::where('is_active', true)
            ->where('start_date', '<=', $targetDate->endOfMonth()->toDateString())
            ->where(function ($q) use ($targetDate) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $targetDate->startOfMonth()->toDateString());
            })
            ->get();

        $generatedCount = 0;
        foreach ($recurring as $rec) {
            $expense = $rec->generateExpense($targetDate);
            if ($expense) {
                $generatedCount++;
                $this->line("  ✓ Generated: [{$expense->expense_number}] {$expense->title} ({$expense->amount} DH)");
            }
        }

        $this->info("Done! {$generatedCount} recurring expense(s) created for {$targetDate->translatedFormat('F Y')}.");

        return Command::SUCCESS;
    }
}

