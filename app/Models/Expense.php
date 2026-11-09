<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_number',
        'title',
        'category',
        'amount',
        'expense_date',
        'payment_method',
        'reference_number',
        'payee',
        'receipt_image',
        'store_id',
        'user_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public static array $categories = [
        'rent' => [
            'label' => 'Loyer & Locatif',
            'badge' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'icon' => 'fas fa-building',
        ],
        'salaries' => [
            'label' => 'Salaires & Rémunérations',
            'badge' => 'bg-info-subtle text-info border border-info-subtle',
            'icon' => 'fas fa-users',
        ],
        'marketing' => [
            'label' => 'Marketing & Ads',
            'badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'icon' => 'fas fa-bullhorn',
        ],
        'shipping' => [
            'label' => 'Transport & Livraison',
            'badge' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'icon' => 'fas fa-truck',
        ],
        'utilities' => [
            'label' => 'Électricité, Eau & Web',
            'badge' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'icon' => 'fas fa-bolt',
        ],
        'packaging' => [
            'label' => 'Emballage & Cartons',
            'badge' => 'bg-dark-subtle text-dark border border-dark-subtle',
            'icon' => 'fas fa-box-open',
        ],
        'maintenance' => [
            'label' => 'Matériel & Réparation',
            'badge' => 'bg-success-subtle text-success border border-success-subtle',
            'icon' => 'fas fa-tools',
        ],
        'taxes' => [
            'label' => 'Taxes, Impôts & Juridique',
            'badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'icon' => 'fas fa-landmark',
        ],
        'bank_fees' => [
            'label' => 'Frais Bancaires',
            'badge' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'icon' => 'fas fa-credit-card',
        ],
        'other' => [
            'label' => 'Divers / Imprévus',
            'badge' => 'bg-light text-secondary border',
            'icon' => 'fas fa-receipt',
        ],
    ];

    public static array $paymentMethods = [
        'cash' => [
            'label' => 'Espèces / Cash',
            'icon' => 'fas fa-money-bill-wave',
        ],
        'bank_transfer' => [
            'label' => 'Virement bancaire',
            'icon' => 'fas fa-building-columns',
        ],
        'check' => [
            'label' => 'Chèque',
            'icon' => 'fas fa-money-check',
        ],
        'credit_card' => [
            'label' => 'Carte bancaire',
            'icon' => 'fas fa-credit-card',
        ],
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::$categories[$this->category]['label'] ?? ucfirst($this->category);
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return self::$categories[$this->category]['badge'] ?? 'bg-light text-secondary border';
    }

    public function getCategoryIconAttribute(): string
    {
        return self::$categories[$this->category]['icon'] ?? 'fas fa-receipt';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::$paymentMethods[$this->payment_method]['label'] ?? ucfirst($this->payment_method);
    }

    public function getPaymentMethodIconAttribute(): string
    {
        return self::$paymentMethods[$this->payment_method]['icon'] ?? 'fas fa-money-bill';
    }

    public function getReceiptUrlAttribute(): ?string
    {
        if (!$this->receipt_image) {
            return null;
        }

        if (str_starts_with($this->receipt_image, 'http://') || str_starts_with($this->receipt_image, 'https://')) {
            return $this->receipt_image;
        }

        return Storage::disk('public')->url($this->receipt_image);
    }

    /**
     * Generate an expense number: DEP-YYYYMM-XXXX
     */
    public static function generateExpenseNumber(): string
    {
        $prefix = 'DEP-' . date('Ym') . '-';
        $latest = self::where('expense_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('expense_number');

        if ($latest) {
            $number = intval(substr($latest, -4)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad((string)$number, 4, '0', STR_PAD_LEFT);
    }
}
