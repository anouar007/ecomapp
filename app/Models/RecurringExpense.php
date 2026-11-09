<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'amount',
        'frequency',
        'day_of_month',
        'start_date',
        'end_date',
        'payment_method',
        'payee',
        'store_id',
        'user_id',
        'notes',
        'is_active',
        'auto_generate',
        'last_generated_at',
        'next_due_date',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'day_of_month'      => 'integer',
        'start_date'        => 'date',
        'end_date'          => 'date',
        'last_generated_at' => 'date',
        'next_due_date'     => 'date',
        'is_active'         => 'boolean',
        'auto_generate'     => 'boolean',
    ];

    public static array $frequencies = [
        'monthly'   => ['label' => 'Mensuelle (Tous les mois)', 'interval' => '1 month'],
        'quarterly' => ['label' => 'Trimestrielle (Tous les 3 mois)', 'interval' => '3 months'],
        'yearly'    => ['label' => 'Annuelle (Tous les ans)', 'interval' => '1 year'],
        'weekly'    => ['label' => 'Hebdomadaire (Toutes les semaines)', 'interval' => '1 week'],
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generatedExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'recurring_expense_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return Expense::$categories[$this->category]['label'] ?? ucfirst($this->category);
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return Expense::$categories[$this->category]['badge'] ?? 'bg-light text-secondary border';
    }

    public function getCategoryIconAttribute(): string
    {
        return Expense::$categories[$this->category]['icon'] ?? 'fas fa-receipt';
    }

    public function getFrequencyLabelAttribute(): string
    {
        return self::$frequencies[$this->frequency]['label'] ?? ucfirst($this->frequency);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return Expense::$paymentMethods[$this->payment_method]['label'] ?? ucfirst($this->payment_method);
    }

    /**
     * Check if an expense has already been generated for the given month.
     */
    public function isGeneratedForMonth(Carbon $date): bool
    {
        return $this->generatedExpenses()
            ->whereYear('expense_date', $date->year)
            ->whereMonth('expense_date', $date->month)
            ->exists();
    }

    /**
     * Generate actual Expense record for the given target date/month.
     */
    public function generateExpense(Carbon $targetDate): ?Expense
    {
        if ($this->isGeneratedForMonth($targetDate)) {
            return null; // Already generated
        }

        // Clamp day to valid days in month (e.g. 31 -> 28/29 for Feb)
        $day = min($this->day_of_month ?: 1, $targetDate->daysInMonth);
        $expenseDate = $targetDate->copy()->day($day);

        $expense = Expense::create([
            'expense_number'       => Expense::generateExpenseNumber(),
            'title'                => $this->title,
            'category'             => $this->category,
            'amount'               => $this->amount,
            'expense_date'         => $expenseDate->toDateString(),
            'payment_method'       => $this->payment_method,
            'reference_number'     => 'REC-' . $this->id . '-' . $expenseDate->format('Ym'),
            'payee'                => $this->payee,
            'store_id'             => $this->store_id,
            'user_id'              => $this->user_id ?? auth()->id(),
            'recurring_expense_id' => $this->id,
            'status'               => 'paid',
            'notes'                => "Généré automatiquement depuis la charge récurrente mensuelle (#{$this->id}).",
        ]);

        $this->update([
            'last_generated_at' => $expenseDate->toDateString(),
            'next_due_date'     => $this->computeNextDueDate($expenseDate),
        ]);

        return $expense;
    }

    /**
     * Compute next due date after a given date.
     */
    public function computeNextDueDate(Carbon $fromDate): Carbon
    {
        $next = match ($this->frequency) {
            'quarterly' => $fromDate->copy()->addMonths(3),
            'yearly'    => $fromDate->copy()->addYear(),
            'weekly'    => $fromDate->copy()->addWeek(),
            default     => $fromDate->copy()->addMonth(),
        };

        $day = min($this->day_of_month ?: 1, $next->daysInMonth);
        return $next->day($day);
    }
}
