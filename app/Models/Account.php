<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'class',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'class' => 'integer',
    ];

    /**
     * Get the lines associated with the account.
     */
    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    /**
     * Get the current balance of the account.
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    public function getBalance($startDate = null, $endDate = null)
    {
        $query = $this->lines();

        if ($startDate) {
            $query->whereHas('entry', function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('entry', function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }

        $debit = $query->sum('debit');
        $credit = $query->sum('credit');

        // Asset, Expense: Debit increases (Debit - Credit)
        // Liability, Equity, Revenue: Credit increases (Credit - Debit)
        if (in_array($this->type, ['Asset', 'Expense'])) {
            return $debit - $credit;
        }

        return $credit - $debit;
    }
}
