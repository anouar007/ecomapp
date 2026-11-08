<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CustomerTransaction extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'customer_id',
        'transaction_number',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'payment_method',
        'reference_number',
        'proof_image',
        'order_id',
        'invoice_id',
        'payment_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Customer associated with this transaction.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * User who recorded this transaction.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Associated order (if any).
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Associated invoice (if any).
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Generate unique transaction number (TRX-YYYYMMDD-XXXX).
     */
    public static function generateTransactionNumber(): string
    {
        $prefix = 'TRX-' . date('Ymd') . '-';
        $last = self::where('transaction_number', 'LIKE', "{$prefix}%")
                    ->orderBy('id', 'desc')
                    ->first();

        $nextSeq = 1;
        if ($last) {
            $parts = explode('-', $last->transaction_number);
            $lastSeq = (int) end($parts);
            $nextSeq = $lastSeq + 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Public URL for proof of payment.
     */
    public function getProofImageUrlAttribute(): ?string
    {
        if (!$this->proof_image) {
            return null;
        }

        if (str_starts_with($this->proof_image, 'http://') || str_starts_with($this->proof_image, 'https://')) {
            return $this->proof_image;
        }

        return asset('storage/' . $this->proof_image);
    }

    /**
     * Type readable label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'payment_received' => 'Règlement Client (Encaissé)',
            'refund_to_client' => 'Remboursement versé au client',
            'credit_given' => 'Crédit accordé (Vente à crédit)',
            'credit_note' => 'Avoir accordé (Retour/Remise)',
            'adjustment' => 'Ajustement de solde',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Direction of the cash flow: 'in' (money into store) or 'out' (money out of store).
     */
    public function getDirectionAttribute(): string
    {
        return match($this->type) {
            'payment_received' => 'in',
            'refund_to_client' => 'out',
            'credit_given' => 'out',
            'credit_note' => 'in',
            default => 'neutral',
        };
    }

    /**
     * Badge CSS class for the transaction type.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'payment_received' => 'bg-success-subtle text-success border-success-subtle',
            'refund_to_client' => 'bg-danger-subtle text-danger border-danger-subtle',
            'credit_given' => 'bg-warning-subtle text-warning border-warning-subtle',
            'credit_note' => 'bg-info-subtle text-info border-info-subtle',
            'adjustment' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
            default => 'bg-light text-dark',
        };
    }

    /**
     * Payment method readable label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'especes' => 'Espèces / Cash',
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque bancaire',
            'tpe' => 'Carte bancaire (TPE)',
            'versement' => 'Versement bancaire',
            'avoir' => 'Déduction sur Avoir',
            default => ucfirst($this->payment_method),
        };
    }
}

