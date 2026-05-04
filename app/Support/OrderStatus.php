<?php

namespace App\Support;

final class OrderStatus
{
    /** @var array<string, string> */
    public const LABELS = [
        'pending' => 'Ожидает оплату',
        'paid' => 'Оплачен',
        'in_delivery' => 'В доставке',
        'arrived' => 'Курьер на месте',
        'delivered' => 'Доставлен',
        'cancelled' => 'Отменён',
    ];

    public static function label(string $status): string
    {
        return self::LABELS[$status] ?? $status;
    }

    public static function badgeClass(string $status): string
    {
        return match ($status) {
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'paid' => 'bg-sky-50 text-sky-700 border-sky-200',
            'in_delivery' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'arrived' => 'bg-violet-50 text-violet-700 border-violet-200',
            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'cancelled' => 'bg-stone-100 text-stone-600 border-stone-200',
            default => 'bg-stone-100 text-stone-700 border-stone-200',
        };
    }
}
