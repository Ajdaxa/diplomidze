<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'courier_id',
        'total_price',
        'status',
        'address',
        'leave_at_door',
        'delivery_photo',
        'promocode_id',
        'yookassa_payment_id',
        'paid_at',
    ];

    protected $casts = [
        'address' => 'array',
        'leave_at_door' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function requiresDoorPhoto(): bool
    {
        return (bool) $this->leave_at_door;
    }

    public function deliveryPhotoUrl(): ?string
    {
        if (! $this->delivery_photo) {
            return null;
        }

        return asset('storage/'.$this->delivery_photo);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(Promocode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canCancel(): bool
    {
        return ! in_array($this->status, ['delivered', 'cancelled'], true);
    }

    /**
     * Отмена незавершённого заказа: если остатки уже списаны (оплачен и дальше), возвращаем на склад.
     */
    public static function cancelUnfinished(self $order): void
    {
        DB::transaction(function () use ($order): void {
            /** @var self|null $locked */
            $locked = static::query()->with('items')->lockForUpdate()->find($order->id);
            if (! $locked || ! $locked->canCancel()) {
                return;
            }

            if (in_array($locked->status, ['paid', 'in_delivery', 'arrived'], true)) {
                foreach ($locked->items as $item) {
                    $product = Product::query()->lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->increment('stock', (int) $item->quantity);
                    }
                }
            }

            $locked->update([
                'status' => 'cancelled',
                'courier_id' => null,
            ]);
        });
    }
}
