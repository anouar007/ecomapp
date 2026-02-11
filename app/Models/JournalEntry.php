<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'reference',
        'description',
        'journal_type',
        'fiscal_year',
    ];

    protected $casts = [
        'date' => 'date',
        'fiscal_year' => 'integer',
    ];

    /**
     * Get the lines for the journal entry.
     */
    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    /**
     * Get the total debit amount.
     */
    public function getTotalDebitAttribute()
    {
        return $this->lines->sum('debit');
    }

    /**
     * Get the total credit amount.
     */
    public function getTotalCreditAttribute()
    {
        return $this->lines->sum('credit');
    }

    /**
     * Check if the entry is balanced.
     */
    public function isBalanced()
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }
}
