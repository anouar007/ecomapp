<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'event',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo('subject', 'subject_type', 'subject_id');
    }

    /**
     * Log an activity.
     */
    public static function log(string $event, $subject = null, array $properties = [], ?string $description = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id ?? null,
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get changes in human-readable format.
     */
    public function getChangesAttribute(): array
    {
        $old = $this->properties['old'] ?? [];
        $new = $this->properties['new'] ?? [];

        if (empty($old) && empty($new)) {
            return [];
        }

        $changes = [];

        if ($this->event === 'created') {
            foreach ($new as $key => $newValue) {
                $changes[$key] = [
                    'old' => null,
                    'new' => $newValue,
                ];
            }
            return $changes;
        }

        if ($this->event === 'deleted') {
            foreach ($old as $key => $oldValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => null,
                ];
            }
            return $changes;
        }

        // Updated
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
        foreach ($allKeys as $key) {
            $oldValue = $old[$key] ?? null;
            $newValue = $new[$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    public function getEventBadgeAttribute(): string
    {
        return match($this->event) {
            'created' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1"><i class="fas fa-plus-circle me-1"></i>Création</span>',
            'updated' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1"><i class="fas fa-pen-to-square me-1"></i>Modification</span>',
            'deleted' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1"><i class="fas fa-trash-can me-1"></i>Suppression</span>',
            'login' => '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1"><i class="fas fa-right-to-bracket me-1"></i>Connexion</span>',
            'logout' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1"><i class="fas fa-right-from-bracket me-1"></i>Déconnexion</span>',
            'password_reset' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1"><i class="fas fa-key me-1"></i>Mot de passe</span>',
            default => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1">' . htmlspecialchars(ucfirst($this->event)) . '</span>',
        };
    }

    public function getSubjectLabelAttribute(): string
    {
        if (!$this->subject_type) return 'Système';
        $base = class_basename($this->subject_type);
        $name = match($base) {
            'Product' => 'Produit',
            'ProductVariant' => 'Variante',
            'Category' => 'Catégorie',
            'Order' => 'Commande',
            'Invoice' => 'Facture',
            'Customer' => 'Client',
            'Coupon' => 'Code Promo',
            'User' => 'Utilisateur',
            'Setting' => 'Paramètre',
            'InventoryMovement' => 'Mouvement / Transfert Stock',
            'Expense' => 'Dépense d\'Exploitation',
            'RecurringExpense' => 'Charge Récurrente Fixe',
            'CustomerTransaction' => 'Règlement / Opération Client',
            'Store' => 'Point de Stockage / Dépôt',
            'ProductStock' => 'Stock & Emplacement',
            'Banner' => 'Bannière',
            'Brand' => 'Marque',
            'ProductReview' => 'Avis',
            'ReturnOrder' => 'Retour',
            default => $base,
        };
        return $this->subject_id ? "{$name} #{$this->subject_id}" : $name;
    }

    /**
     * Get direct source deep-link URL in admin if applicable.
     */
    public function getSourceUrlAttribute(): ?string
    {
        if (!$this->subject_type || !$this->subject_id) {
            return null;
        }

        $base = class_basename($this->subject_type);

        try {
            return match($base) {
                'Customer' => route('customers.show', $this->subject_id),
                'CustomerTransaction' => route('client-credits.transactions', ['search' => $this->subject_id]),
                'Expense' => route('expenses.index', ['search' => $this->subject_id]),
                'RecurringExpense' => route('expenses.index', ['tab' => 'recurring']),
                'Store' => route('stores.index'),
                'Product' => route('products.edit', $this->subject_id),
                'ProductStock', 'InventoryMovement' => route('stores.stock'),
                'Order' => route('orders.show', $this->subject_id),
                'Invoice' => route('invoices.show', $this->subject_id),
                'User' => route('users.index'),
                default => null,
            };
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getEventColorAttribute(): string
    {
        return match($this->event) {
            'created' => 'success',
            'updated' => 'primary',
            'deleted' => 'danger',
            'viewed' => 'info',
            'login' => 'success',
            'logout' => 'secondary',
            default => 'secondary',
        };
    }
}
