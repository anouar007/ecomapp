<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, \App\Traits\LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'affiliate_points',
        'phone',
        'job_title',
        'status',
        'avatar',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'affiliate_points' => 'integer',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Determine if the user is active.
     */
    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    /**
     * Determine if the user is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Get avatar url or fallback initials.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return str_starts_with($this->avatar, 'http') ? $this->avatar : asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Get user status badge HTML.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'inactive' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill"><i class="fas fa-pause-circle me-1"></i>Inactif</span>',
            'suspended' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill"><i class="fas fa-ban me-1"></i>Suspendu</span>',
            default => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill"><i class="fas fa-check-circle me-1"></i>Actif</span>',
        };
    }

    /**
     * Get effective permission names (via roles + direct permissions).
     */
    public function allEffectivePermissions(): \Illuminate\Support\Collection
    {
        return $this->getAllPermissions()->pluck('name');
    }

    /**
     * Filter active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Search scope for team & user management.
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('job_title', 'like', "%{$search}%");
        });
    }

    /**
     * Get the user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the affiliate coupons created for this user.
     */
    public function affiliateCoupons()
    {
        return $this->hasMany(Coupon::class, 'affiliate_user_id');
    }

    /**
     * Get the user's wishlist.
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Check if product is in wishlist.
     */
    public function inWishlist($productId)
    {
        return $this->wishlist()->where('product_id', $productId)->exists();
    }
}
