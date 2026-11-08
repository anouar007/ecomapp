<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * Boot the trait to attach Eloquent lifecycle observers.
     */
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->recordActivityLogEntry('created');
        });

        static::updated(function ($model) {
            $model->recordActivityLogEntry('updated');
        });

        static::deleted(function ($model) {
            $model->recordActivityLogEntry('deleted');
        });
    }

    /**
     * Record activity log entry with old data, new data, user, and date.
     */
    public function recordActivityLogEntry(string $event): void
    {
        // Don't track during migrations/seeders when no user is authenticated
        if (!Auth::check() && app()->runningInConsole()) {
            return;
        }

        try {
            $hiddenFields = array_merge(
                $this->getHidden(),
                ['remember_token', 'updated_at', 'created_at']
            );

            $oldValues = [];
            $newValues = [];
            $diffSummary = [];

            $modelLabel = $this->getActivityModelLabel();
            $ident = $this->getActivityIdentifier();
            $identStr = $ident ? " ({$ident})" : '';

            if ($event === 'created') {
                $attrs = $this->attributesToArray();
                foreach ($attrs as $key => $val) {
                    if (in_array($key, $hiddenFields)) continue;
                    $newValues[$key] = $val;
                }
                $description = "Création de {$modelLabel} #{$this->getKey()}{$identStr}";
            } elseif ($event === 'updated') {
                $dirty = $this->getDirty();
                $original = $this->getOriginal();

                foreach ($dirty as $key => $newVal) {
                    if (in_array($key, $hiddenFields)) continue;

                    $oldVal = $original[$key] ?? null;

                    // If password changed, mask it
                    if ($key === 'password') {
                        $oldValues[$key] = '********';
                        $newValues[$key] = '********';
                        $diffSummary[] = "mot de passe modifié";
                        continue;
                    }

                    // Strict compare
                    if ($oldVal != $newVal) {
                        $oldValues[$key] = $oldVal;
                        $newValues[$key] = $newVal;

                        $strOld = is_array($oldVal) ? json_encode($oldVal, JSON_UNESCAPED_UNICODE) : (string)$oldVal;
                        $strNew = is_array($newVal) ? json_encode($newVal, JSON_UNESCAPED_UNICODE) : (string)$newVal;

                        if (mb_strlen($strOld) > 25) $strOld = mb_substr($strOld, 0, 22) . '...';
                        if (mb_strlen($strNew) > 25) $strNew = mb_substr($strNew, 0, 22) . '...';

                        $diffSummary[] = "{$key} : '{$strOld}' ➔ '{$strNew}'";
                    }
                }

                // If no actual business field changed, do not log
                if (empty($newValues)) {
                    return;
                }

                $summaryText = !empty($diffSummary) ? ' [' . implode(', ', array_slice($diffSummary, 0, 3)) . (count($diffSummary) > 3 ? '...' : '') . ']' : '';
                $description = "Modification de {$modelLabel} #{$this->getKey()}{$identStr}{$summaryText}";
            } elseif ($event === 'deleted') {
                $attrs = $this->attributesToArray();
                foreach ($attrs as $key => $val) {
                    if (in_array($key, $hiddenFields)) continue;
                    if ($key === 'password') continue;
                    $oldValues[$key] = $val;
                }
                $description = "Suppression de {$modelLabel} #{$this->getKey()}{$identStr}";
            } else {
                return;
            }

            $user = Auth::user();

            ActivityLog::create([
                'user_id' => $user?->id ?? null,
                'subject_type' => get_class($this),
                'subject_id' => $this->getKey(),
                'event' => $event,
                'description' => $description,
                'properties' => [
                    'old' => $oldValues,
                    'new' => $newValues,
                    'user_name' => $user?->name ?? 'Système',
                    'user_email' => $user?->email ?? null,
                ],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'System',
            ]);
        } catch (\Throwable $e) {
            Log::warning('LogsActivity trait error: ' . $e->getMessage());
        }
    }

    /**
     * Get human-friendly label for model.
     */
    protected function getActivityModelLabel(): string
    {
        $base = class_basename($this);
        return match($base) {
            'Product' => 'Produit',
            'ProductVariant' => 'Variante',
            'Category' => 'Catégorie',
            'Order' => 'Commande',
            'Invoice' => 'Facture',
            'Customer' => 'Client',
            'Coupon' => 'Code Promo',
            'User' => 'Utilisateur',
            'Setting' => 'Paramètre',
            'InventoryMovement' => 'Mouvement Stock',
            'Banner' => 'Bannière',
            'Brand' => 'Marque',
            'ProductReview' => 'Avis',
            'ReturnOrder' => 'Retour',
            'Account' => 'Compte Comptable',
            'JournalEntry' => 'Écriture Journal',
            default => $base,
        };
    }

    /**
     * Get human-friendly identifier (name, title, order_number, etc.).
     */
    protected function getActivityIdentifier(): ?string
    {
        return $this->name 
            ?? $this->title 
            ?? $this->order_number 
            ?? $this->invoice_number 
            ?? $this->code 
            ?? $this->key 
            ?? null;
    }
}
