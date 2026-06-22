<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promocode;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StoreDemoDataSeeder extends Seeder
{
    private const DEMO_EMAIL_DOMAIN = '@demo.dyab';

    public function purgeDemoData(): void
    {
        $userIds = User::query()
            ->where(function ($q): void {
                $q->where('email', 'like', '%'.self::DEMO_EMAIL_DOMAIN)
                    ->orWhereIn('email', [
                        'courier2@dyab.local',
                        'courier3@dyab.local',
                    ]);
            })
            ->pluck('id');

        if ($userIds->isNotEmpty()) {
            Review::query()->whereIn('user_id', $userIds)->delete();
            Order::query()->whereIn('user_id', $userIds)->delete();
            User::query()->whereIn('id', $userIds)->delete();
        }

        $demoCodes = ['DYAB10', 'WELCOME15', 'SPRING500', 'VIP20', 'SUMMER25'];
        Promocode::query()->whereIn('code', $demoCodes)->delete();
    }

    public function run(): void
    {
        $products = Product::query()->where('is_active', true)->orderBy('id')->get();

        if ($products->isEmpty()) {
            $this->command?->error('Нет активных товаров. Сначала запустите php artisan db:seed');

            return;
        }

        Role::findOrCreate('client', 'web');
        Role::findOrCreate('courier', 'web');

        $buyers = $this->ensureDemoBuyers();
        $couriers = $this->ensureDemoCouriers();
        $promocodes = $this->seedPromocodes();

        if (Order::query()->whereIn('user_id', $buyers->pluck('id'))->exists()) {
            $this->command?->warn('Демо-заказы уже есть. Для пересоздания: php artisan store:seed-demo --fresh');
        } else {
            $this->seedOrders($buyers, $products, $couriers, $promocodes);
        }

        Review::query()
            ->whereHas('user', fn ($q) => $q->where('email', 'like', '%'.self::DEMO_EMAIL_DOMAIN))
            ->delete();

        $createdReviews = $this->seedReviews($buyers, $products);

        $this->command?->info(sprintf(
            'Готово: %d покупателей, %d курьеров, %d промокодов, %d заказов, %d отзывов.',
            $buyers->count(),
            $couriers->count(),
            Promocode::query()->whereIn('code', ['DYAB10', 'WELCOME15', 'SPRING500', 'VIP20', 'SUMMER25'])->count(),
            Order::query()->whereIn('user_id', $buyers->pluck('id'))->count(),
            $createdReviews
        ));
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    private function ensureDemoCouriers(): \Illuminate\Support\Collection
    {
        $profiles = [
            ['name' => 'Иван Петров', 'phone' => '+79001234567', 'email' => 'courier@dyab.local', 'commission' => 10],
            ['name' => 'Артём Кузнецов', 'phone' => '+79002345678', 'email' => 'courier2@dyab.local', 'commission' => 12],
            ['name' => 'Максим Орлов', 'phone' => '+79003456789', 'email' => 'courier3@dyab.local', 'commission' => 15],
        ];

        $couriers = collect();

        foreach ($profiles as $profile) {
            $courier = User::query()->updateOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'phone' => $profile['phone'],
                    'password' => Hash::make('Courier12345!'),
                    'role' => 'courier',
                    'courier_commission_percent' => $profile['commission'],
                ]
            );
            $courier->syncRoles(['courier']);
            $couriers->push($courier);
        }

        return $couriers;
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    private function ensureDemoBuyers(): \Illuminate\Support\Collection
    {
        $profiles = [
            ['name' => 'Анна Смирнова', 'phone' => '+79001112233'],
            ['name' => 'Дмитрий Волков', 'phone' => '+79002223344'],
            ['name' => 'Елена Козлова', 'phone' => '+79003334455'],
            ['name' => 'Сергей Морозов', 'phone' => '+79004445566'],
            ['name' => 'Мария Новикова', 'phone' => '+79005556677'],
            ['name' => 'Алексей Соколов', 'phone' => '+79006667788'],
            ['name' => 'Ольга Лебедева', 'phone' => '+79007778899'],
            ['name' => 'Никита Егоров', 'phone' => '+79008889900'],
            ['name' => 'Виктория Павлова', 'phone' => '+79009990011'],
            ['name' => 'Кирилл Фёдоров', 'phone' => '+79001001122'],
            ['name' => 'Дарья Михайлова', 'phone' => '+79002112233'],
            ['name' => 'Роман Белов', 'phone' => '+79003223344'],
        ];

        $users = collect();

        foreach ($profiles as $i => $profile) {
            $email = 'buyer'.($i + 1).self::DEMO_EMAIL_DOMAIN;

            $user = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $profile['name'],
                    'phone' => $profile['phone'],
                    'password' => Hash::make('Demo12345!'),
                    'role' => 'client',
                    'loyalty_points' => random_int(0, 450),
                ]
            );
            $user->syncRoles(['client']);
            $users->push($user);
        }

        return $users;
    }

    /** @return array<string, Promocode> */
    private function seedPromocodes(): array
    {
        $definitions = [
            [
                'code' => 'DYAB10',
                'purpose' => Promocode::PURPOSE_STANDARD,
                'type' => 'percent',
                'value' => 10,
                'max_discount' => 3000,
                'min_order_total' => 5000,
                'usage_limit' => 200,
                'usage_count' => 14,
                'expires_at' => now()->addMonths(4),
            ],
            [
                'code' => 'WELCOME15',
                'purpose' => Promocode::PURPOSE_STANDARD,
                'type' => 'percent',
                'value' => 15,
                'max_discount' => 2500,
                'min_order_total' => 3500,
                'usage_limit' => 500,
                'usage_count' => 38,
                'expires_at' => now()->addYear(),
            ],
            [
                'code' => 'SPRING500',
                'purpose' => Promocode::PURPOSE_STANDARD,
                'type' => 'fixed',
                'value' => 500,
                'max_discount' => null,
                'min_order_total' => 6000,
                'usage_limit' => 100,
                'usage_count' => 7,
                'expires_at' => now()->addMonths(2),
            ],
            [
                'code' => 'VIP20',
                'purpose' => Promocode::PURPOSE_LOYALTY,
                'type' => 'percent',
                'value' => 20,
                'max_discount' => 5000,
                'min_order_total' => 8000,
                'usage_limit' => 50,
                'usage_count' => 3,
                'expires_at' => now()->addMonths(6),
            ],
            [
                'code' => 'SUMMER25',
                'purpose' => Promocode::PURPOSE_STANDARD,
                'type' => 'percent',
                'value' => 25,
                'max_discount' => 4000,
                'min_order_total' => 10000,
                'usage_limit' => 30,
                'usage_count' => 0,
                'expires_at' => now()->addMonths(3),
            ],
        ];

        $map = [];

        foreach ($definitions as $data) {
            $code = $data['code'];
            unset($data['code']);

            $promo = Promocode::query()->updateOrCreate(
                ['code' => $code],
                $data + ['is_active' => true]
            );
            $map[$code] = $promo;
        }

        return $map;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, User>  $buyers
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     * @param  \Illuminate\Support\Collection<int, User>  $couriers
     * @param  array<string, Promocode>  $promocodes
     */
    private function seedOrders(
        \Illuminate\Support\Collection $buyers,
        \Illuminate\Support\Collection $products,
        \Illuminate\Support\Collection $couriers,
        array $promocodes,
    ): void {
        $addresses = [
            ['full' => 'г. Москва, ул. Тверская, 12, кв. 45'],
            ['full' => 'г. Москва, Ленинский проспект, 88, подъезд 3'],
            ['full' => 'г. Санкт-Петербург, Невский проспект, 102'],
            ['full' => 'г. Казань, ул. Баумана, 15, кв. 7'],
            ['full' => 'г. Екатеринбург, ул. Малышева, 36'],
            ['full' => 'г. Москва, ул. Арбат, 24, офис 210'],
        ];

        $scenarios = [
            ['status' => 'delivered', 'days_ago' => 21, 'buyer_idx' => 0, 'product_idxs' => [0], 'promo' => null, 'courier_idx' => 0],
            ['status' => 'delivered', 'days_ago' => 18, 'buyer_idx' => 1, 'product_idxs' => [1, 2], 'promo' => 'DYAB10', 'courier_idx' => 1],
            ['status' => 'delivered', 'days_ago' => 14, 'buyer_idx' => 2, 'product_idxs' => [2], 'promo' => null, 'courier_idx' => 2],
            ['status' => 'delivered', 'days_ago' => 11, 'buyer_idx' => 3, 'product_idxs' => [3], 'promo' => null, 'courier_idx' => 0],
            ['status' => 'delivered', 'days_ago' => 9, 'buyer_idx' => 4, 'product_idxs' => [4, 5], 'promo' => 'WELCOME15', 'courier_idx' => 1],
            ['status' => 'delivered', 'days_ago' => 6, 'buyer_idx' => 5, 'product_idxs' => [0], 'promo' => null, 'courier_idx' => 2],
            ['status' => 'delivered', 'days_ago' => 4, 'buyer_idx' => 6, 'product_idxs' => [6, 7], 'promo' => null, 'courier_idx' => 0],
            ['status' => 'delivered', 'days_ago' => 2, 'buyer_idx' => 7, 'product_idxs' => [8], 'promo' => 'SPRING500', 'courier_idx' => 1],
            ['status' => 'delivered', 'days_ago' => 1, 'buyer_idx' => 8, 'product_idxs' => [9], 'promo' => null, 'courier_idx' => 2, 'leave_at_door' => true],
            ['status' => 'paid', 'days_ago' => 2, 'buyer_idx' => 9, 'product_idxs' => [10], 'promo' => null],
            ['status' => 'paid', 'days_ago' => 1, 'buyer_idx' => 10, 'product_idxs' => [11, 12], 'promo' => null],
            ['status' => 'in_delivery', 'days_ago' => 1, 'buyer_idx' => 0, 'product_idxs' => [13], 'promo' => null, 'courier_idx' => 0],
            ['status' => 'arrived', 'days_ago' => 0, 'buyer_idx' => 1, 'product_idxs' => [14], 'promo' => null, 'courier_idx' => 1],
            ['status' => 'pending', 'days_ago' => 0, 'buyer_idx' => 2, 'product_idxs' => [15], 'promo' => null],
            ['status' => 'cancelled', 'days_ago' => 5, 'buyer_idx' => 3, 'product_idxs' => [16], 'promo' => null],
        ];

        foreach ($scenarios as $scenario) {
            $buyer = $buyers[$scenario['buyer_idx'] % $buyers->count()];
            $createdAt = now()->subDays($scenario['days_ago'])->setTime(random_int(10, 20), random_int(0, 59));
            $courier = isset($scenario['courier_idx'])
                ? $couriers[$scenario['courier_idx'] % $couriers->count()]
                : null;

            $lineItems = [];
            $total = 0.0;

            foreach ($scenario['product_idxs'] as $pIdx) {
                $product = $products[$pIdx % $products->count()];
                $qty = 1;
                $unitPrice = (float) $product->saleUnitPrice();
                $size = $this->pickSize($product);
                $lineItems[] = compact('product', 'qty', 'unitPrice', 'size');
                $total += $unitPrice * $qty;
            }

            $promo = $scenario['promo'] ? ($promocodes[$scenario['promo']] ?? null) : null;
            if ($promo && $promo->type === 'percent') {
                $discount = min($total * ($promo->value / 100), (float) ($promo->max_discount ?? $total));
                $total = max(0, $total - $discount);
            } elseif ($promo && $promo->type === 'fixed') {
                $total = max(0, $total - (float) $promo->value);
            }

            $paidAt = in_array($scenario['status'], ['paid', 'in_delivery', 'arrived', 'delivered'], true)
                ? $createdAt->copy()->addHours(2)
                : null;

            DB::transaction(function () use (
                $buyer,
                $scenario,
                $courier,
                $promo,
                $addresses,
                $lineItems,
                $total,
                $createdAt,
                $paidAt,
            ): void {
                $order = Order::query()->create([
                    'user_id' => $buyer->id,
                    'courier_id' => $courier?->id,
                    'total_price' => round($total, 2),
                    'status' => $scenario['status'],
                    'address' => $addresses[array_rand($addresses)],
                    'leave_at_door' => (bool) ($scenario['leave_at_door'] ?? false),
                    'promocode_id' => $promo?->id,
                    'yookassa_payment_id' => $scenario['status'] === 'pending'
                        ? null
                        : 'demo_pay_'.uniqid(),
                    'paid_at' => $paidAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                foreach ($lineItems as $line) {
                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'product_id' => $line['product']->id,
                        'size' => $line['size'],
                        'quantity' => $line['qty'],
                        'price' => $line['unitPrice'],
                    ]);
                }
            });
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, User>  $buyers
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    private function seedReviews(\Illuminate\Support\Collection $buyers, \Illuminate\Support\Collection $products): int
    {
        $reviewTexts = [
            ['rating' => 5, 'body' => 'Заказывала на мероприятие — село идеально, ткань не мнётся. Доставка пришла на следующий день.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Крой структурный, на прохладный вечер самое то. Плечи чуть свободнее, чем на фото.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Размер подошёл с первого раза, таблица размеров помогла. Швы ровные.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Подарил супруге — восторг. Упаковка аккуратная, без лишнего пластика.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Цвет совпал с карточкой товара. После стирки форма не изменилась.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'В поддержке быстро подсказали по размеру. Попал в размер с первого раза.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 3, 'body' => 'Качество хорошее, доставка заняла на день дольше. Курьер заранее позвонил.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Первый заказ в Дəb — приятно удивили скоростью и связью по статусу.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Удобно на прогулке, материал приятный. Очень доволен покупкой.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Лимитированная позиция — рад, что успел. Выглядит дороже своей цены.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Сумка вместительная, молния ходит плавно. Ношу каждый день.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Отличное соотношение цены и качества. Вернусь за базовыми вещами.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Минималистичный стиль, как на сайте. Сочетается с разной одеждой.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Товар понравился, жду публикации отзыва — отправила два дня назад.', 'status' => Review::STATUS_PENDING],
            ['rating' => 5, 'body' => 'Взяла со скидкой по промокоду — вышло выгодно. Посадка аккуратная.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Курьер оставил у двери, как просила. Всё пришло в целости.', 'status' => Review::STATUS_PENDING],
        ];

        $count = 0;
        $deliveredPairs = Order::query()
            ->where('status', 'delivered')
            ->whereHas('user', fn ($q) => $q->where('email', 'like', '%'.self::DEMO_EMAIL_DOMAIN))
            ->with(['items', 'user'])
            ->latest('id')
            ->get()
            ->flatMap(fn (Order $order) => $order->items->map(fn ($item) => [
                'user_id' => $order->user_id,
                'product_id' => $item->product_id,
            ]))
            ->unique(fn (array $row) => $row['user_id'].'-'.$row['product_id'])
            ->values();

        foreach ($reviewTexts as $i => $text) {
            $pair = $deliveredPairs[$i % max(1, $deliveredPairs->count())] ?? null;

            $userId = $pair['user_id'] ?? $buyers[$i % $buyers->count()]->id;
            $productId = $pair['product_id'] ?? $products[$i % $products->count()]->id;

            Review::query()->updateOrCreate(
                [
                    'product_id' => $productId,
                    'user_id' => $userId,
                ],
                [
                    'rating' => $text['rating'],
                    'body' => $text['body'],
                    'status' => $text['status'],
                    'created_at' => now()->subDays(random_int(1, 20)),
                    'updated_at' => now()->subDays(random_int(0, 5)),
                ]
            );
            $count++;
        }

        return $count;
    }

    private function pickSize(Product $product): ?string
    {
        $sizes = $product->available_sizes ?? [];

        if (is_array($sizes) && $sizes !== []) {
            return $sizes[array_key_first($sizes)] ?? $product->size;
        }

        return $product->size;
    }
}
