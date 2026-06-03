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
            ->where('email', 'like', '%'.self::DEMO_EMAIL_DOMAIN)
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

        $buyers = $this->ensureDemoBuyers();
        $courier = User::query()->where('email', 'courier@dyab.local')->first();
        $promocodes = $this->seedPromocodes();

        if (Order::query()->whereIn('user_id', $buyers->pluck('id'))->exists()) {
            $this->command?->warn('Демо-заказы уже есть. Для пересоздания: php artisan store:seed-demo --fresh');
        } else {
            $this->seedOrders($buyers, $products, $courier, $promocodes);
        }

        $createdReviews = $this->seedReviews($buyers, $products);

        $this->command?->info(sprintf(
            'Готово: %d покупателей, %d промокодов, %d заказов, %d отзывов.',
            $buyers->count(),
            Promocode::query()->whereIn('code', ['DYAB10', 'WELCOME15', 'SPRING500', 'VIP20', 'SUMMER25'])->count(),
            Order::query()->whereIn('user_id', $buyers->pluck('id'))->count(),
            $createdReviews
        ));
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    private function ensureDemoBuyers(): \Illuminate\Support\Collection
    {
        $profiles = [
            ['name' => 'Лейла Мамедова', 'phone' => '+994501234567'],
            ['name' => 'Руслан Гасанов', 'phone' => '+994502345678'],
            ['name' => 'Айгюн Керимова', 'phone' => '+994503456789'],
            ['name' => 'Эмин Ибрагимов', 'phone' => '+994504567890'],
            ['name' => 'Сабина Алиева', 'phone' => '+994505678901'],
            ['name' => 'Тимур Набиев', 'phone' => '+994506789012'],
            ['name' => 'Гюльнара Рзаева', 'phone' => '+994507890123'],
            ['name' => 'Камран Сулейманов', 'phone' => '+994508901234'],
            ['name' => 'Динара Гусейнова', 'phone' => '+994509012345'],
            ['name' => 'Орхан Мирзоев', 'phone' => '+994510123456'],
            ['name' => 'Наргиз Ахмедова', 'phone' => '+994511234567'],
            ['name' => 'Вугар Бабаев', 'phone' => '+994512345678'],
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
     * @param  array<string, Promocode>  $promocodes
     */
    private function seedOrders(
        \Illuminate\Support\Collection $buyers,
        \Illuminate\Support\Collection $products,
        ?User $courier,
        array $promocodes,
    ): void {
        $addresses = [
            ['full' => 'г. Баку, ул. Низами, 45, кв. 12'],
            ['full' => 'г. Баку, пр. Нефтяников, 102'],
            ['full' => 'г. Сумгаит, мкр. 26, д. 8, кв. 34'],
            ['full' => 'г. Баку, ул. Ахмедли, 15, подъезд 2'],
            ['full' => 'г. Гянджа, ул. Ататюрка, 77'],
            ['full' => 'г. Баку, бул. Нахичеванский, 3, офис 210'],
        ];

        $scenarios = [
            ['status' => 'delivered', 'days_ago' => 18, 'buyer_idx' => 0, 'product_idxs' => [0], 'promo' => null],
            ['status' => 'delivered', 'days_ago' => 14, 'buyer_idx' => 1, 'product_idxs' => [1, 2], 'promo' => 'DYAB10'],
            ['status' => 'delivered', 'days_ago' => 11, 'buyer_idx' => 2, 'product_idxs' => [2], 'promo' => null],
            ['status' => 'delivered', 'days_ago' => 9, 'buyer_idx' => 3, 'product_idxs' => [3], 'promo' => null],
            ['status' => 'delivered', 'days_ago' => 6, 'buyer_idx' => 4, 'product_idxs' => [4, 5], 'promo' => 'WELCOME15'],
            ['status' => 'delivered', 'days_ago' => 3, 'buyer_idx' => 5, 'product_idxs' => [0], 'promo' => null],
            ['status' => 'paid', 'days_ago' => 2, 'buyer_idx' => 6, 'product_idxs' => [6], 'promo' => null],
            ['status' => 'paid', 'days_ago' => 1, 'buyer_idx' => 7, 'product_idxs' => [7, 8], 'promo' => null],
            ['status' => 'in_delivery', 'days_ago' => 1, 'buyer_idx' => 8, 'product_idxs' => [9], 'promo' => null, 'courier' => true],
            ['status' => 'arrived', 'days_ago' => 0, 'buyer_idx' => 9, 'product_idxs' => [10], 'promo' => null, 'courier' => true],
            ['status' => 'pending', 'days_ago' => 0, 'buyer_idx' => 10, 'product_idxs' => [11], 'promo' => null],
            ['status' => 'cancelled', 'days_ago' => 5, 'buyer_idx' => 11, 'product_idxs' => [12], 'promo' => null],
        ];

        foreach ($scenarios as $scenario) {
            $buyer = $buyers[$scenario['buyer_idx'] % $buyers->count()];
            $createdAt = now()->subDays($scenario['days_ago'])->setTime(random_int(10, 20), random_int(0, 59));

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
                    'courier_id' => ($scenario['courier'] ?? false) && $courier ? $courier->id : null,
                    'total_price' => round($total, 2),
                    'status' => $scenario['status'],
                    'address' => $addresses[array_rand($addresses)],
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
            ['rating' => 5, 'body' => 'Заказывала на мероприятие — село идеально, ткань не мнётся после дороги. Доставка в Баку заняла два дня.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Крой структурный, на прохладный вечер самое то. Единственное — плечи чуть свободнее, чем на фото модели.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Размер подошёл с первого раза, таблица размеров на сайте помогла. Ношу уже третью неделю — швы ровные.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Подарил супруге — восторг. Упаковка без лишнего пластика, всё аккуратно сложено.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Цвет совпал с карточкой товара. После деликатной стирки форма не изменилась.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Долго выбирал между M и L — в поддержке быстро подсказали по обхвату. Попал в размер.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 3, 'body' => 'Качество хорошее, но доставка в регион заняла на день дольше обещанного. Курьер заранее позвонил.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Первый заказ в Дəb — приятно удивили скоростью сборки и связью по статусу.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Удобно на длинной прогулке, стелька мягкая. Шнурки хотелось бы чуть плотнее.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Лимитированная позиция — рад, что успел. Материал плотный, выглядит дороже цены.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Сумка вместительная, молния ходит плавно. Кожзам чуть жёстче, чем ожидала, но носится отлично.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Отличное соотношение цены и ткани. Вернусь за базовыми вещами в чёрном.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 5, 'body' => 'Минималистичный стиль, как на лукбуке. Сочетается с джинсами и юбкой-карандаш.', 'status' => Review::STATUS_APPROVED],
            ['rating' => 4, 'body' => 'Товар понравился, жду публикации отзыва на сайте — отправила два дня назад.', 'status' => Review::STATUS_PENDING],
            ['rating' => 5, 'body' => 'Взяла со скидкой по промокоду — вышло выгодно. Посадка по фигуре аккуратная.', 'status' => Review::STATUS_APPROVED],
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
