<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'return_number', 'order_id', 'customer_id', 'reason',
        'description', 'refund_amount', 'status', 'refund_method',
        'items', 'processed_by', 'processed_at', 'admin_notes',
    ];

    protected $casts = [
        'items' => 'array',
        'refund_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($return) {
            if (!$return->return_number) {
                $return->return_number = self::generateReturnNumber();
            }
        });
    }

    public static function generateReturnNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastReturn = self::where('return_number', 'LIKE', "RET-{$year}-{$month}-%")
            ->orderBy('return_number', 'desc')
            ->first();

        if ($lastReturn) {
            $lastNumber = (int) substr($lastReturn->return_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('RET-%s-%s-%04d', $year, $month, $nextNumber);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approve($userId, $refundMethod, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'refund_method' => $refundMethod,
            'processed_by' => $userId,
            'processed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }

    public function reject($userId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $userId,
            'processed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getFormattedRefundAmountAttribute(): string
    {
        return currency($this->refund_amount);
    }

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'defective' => 'Defective Product',
            'wrong_item' => 'Wrong Item Shipped',
            'not_as_described' => 'Not As Described',
            'changed_mind' => 'Changed Mind',
            'other' => 'Other',
            default => ucfirst($this->reason),
        };
    }
}
