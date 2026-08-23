<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_no',
        'customer_id',
        'payment_method',
        'status',
        'total',
        'shipping_address',
        'shipping_phone',
        'payment_proof_path',
        'validated_by',
        'validated_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'validated_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => $this->payment_method === 'transfer' ? 'Menunggu Bukti Transfer' : 'Menunggu Konfirmasi Kasir',
            'menunggu_validasi' => 'Menunggu Validasi',
            'diproses' => 'Diproses',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending' => 'secondary',
            'menunggu_validasi' => 'warning',
            'diproses' => 'info',
            'dikirim' => 'primary',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
            default => 'secondary',
        };
    }
}
