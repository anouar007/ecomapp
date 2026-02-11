<?php

namespace App\Exports;

use App\Models\JournalEntryLine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GeneralLedgerExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $accountId;

    public function __construct($startDate, $endDate, $accountId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->accountId = $accountId;
    }

    public function collection()
    {
        $query = JournalEntryLine::with(['entry', 'account'])
            ->whereHas('entry', function($q) {
                $q->whereBetween('date', [$this->startDate, $this->endDate]);
            });

        if ($this->accountId) {
            $query->where('account_id', $this->accountId);
        }

        return $query->get()->sortBy(function($line) {
            return $line->entry->date;
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Journal',
            'Reference',
            'Account Code',
            'Account Name',
            'Description',
            'Debit',
            'Credit',
        ];
    }

    public function map($line): array
    {
        return [
            $line->entry->date->format('Y-m-d'),
            $line->entry->journal_type, // Assuming journal_type exists on entry, or use default
            $line->entry->reference,
            $line->account->code,
            $line->account->name,
            $line->entry->description,
            $line->debit,
            $line->credit,
        ];
    }
}
