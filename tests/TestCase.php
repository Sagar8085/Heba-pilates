<?php

namespace Tests;

use App\Collections\LeadCollection;
use App\Collections\OrderCollection;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Gym;
use App\Models\Lead;
use App\Models\Member;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReformerBooking;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    protected function signIn(User $user = null, string $guard = 'api'): TestCase
    {
        return $this->actingAs($user ?: $this->createAdministrator(), $guard);
    }

    protected function createAdministrator(): User
    {
        /** @var User $user */
        $user = User::factory(1)->createOneQuietly([
            'role_id' => 1,
            'first_name' => 'Stephen',
            'last_name' => 'Medd',
            'phone_number' => '07480305234',
            'gender' => 'Male',
            'password' => Hash::make('0946Duck'),
            'date_of_birth' => '1995-07-14',
        ]);

        $user->memberProfile()->create([
            'contract_path' => Str::random(),
        ]);

        return $user;
    }

    protected function createUser(array $data = [], int $want = 1): User|Collection
    {
        $users = User::factory()
            ->count($want)
            ->createQuietly(
                array_merge(
                    $data,
                    [
                        'guest_status' => 'Prospect',
                    ]
                )
            );

        $users->each(fn (User $user) => $user->memberProfile()->create([
            'contract_path' => Str::random(),
        ]));

        return $want === 1 ? $users->first() : $users->fresh();
    }

    protected function createOrderForSubscription(array $data = [], int $want = null): OrderCollection|Model
    {
        return Order::factory()
            ->forSubscription()
            ->count($want)
            ->create($data);
    }

    protected function createOrderForRenewedSubscription(array $data = [], int $want = null): OrderCollection|Model
    {
        return tap(
            Order::factory()->forSubscription()->count($want)->create($data),
            fn (OrderCollection $orders) => $orders->each(fn (Order $order) => $order->orderable->update([
                'renew' => 1,
            ]))
        );
    }

    protected function createOrderForVipSubscription(array $data = [], int $want = null): OrderCollection|Model
    {
        $subscription = Subscription::factory()->vipUnlimited()->create();

        $orders = $this->createOrder($want, array_merge($data, [
            'orderable_id' => $subscription->id,
        ]));

        $orders->each(fn (Order $order) => $order->orderable->update([
            'renew' => 1,
        ]));

        return $orders;
    }

    protected function createOrderForUnlimitedMonthlySubscription(
        array $data = [],
        int $want = null
    ): OrderCollection|Model {
        $subscription = Subscription::factory()->unlimitedMonthly()->create();

        $orders = $this->createOrder($want, array_merge($data, [
            'orderable_id' => $subscription->id,
        ]));

        $orders->each(fn (Order $order) => $order->orderable->update([
            'renew' => 1,
        ]));

        return $orders;
    }

    protected function createOrderForPremiumMonthlySubscription(
        array $data = [],
        int $want = null
    ): OrderCollection|Model {
        $subscription = Subscription::factory()->premiumMonthly()->create();

        $orders = $this->createOrder($want, array_merge($data, [
            'orderable_id' => $subscription->id,
        ]));

        $orders->each(fn (Order $order) => $order->orderable->update([
            'renew' => 1,
        ]));

        return $orders;
    }

    protected function createOrderForStandardMembership(
        array $data = [],
        int $want = null
    ): OrderCollection|Model {
        $subscription = Subscription::factory()->standard()->create();

        $orders = $this->createOrder($want, array_merge($data, [
            'orderable_id' => $subscription->id,
        ]));

        $orders->each(fn (Order $order) => $order->orderable->update([
            'renew' => 1,
        ]));

        return $orders;
    }

    protected function createOrderForThirtyPackCreditGuests(
        array $data = [],
        int $want = null
    ): OrderCollection|Model {
        return tap(
            Order::factory()->forSubscription()->count($want)->create($data),
            function (OrderCollection $orders) {
                $orders->each(function (Order $order) {
                    $creditPackPurchase = CreditPackPurchase::factory()->create([
                        'order_id' => $order->id,
                        'credit_pack_id' => CreditPack::factory()->create([
                            'name' => '30 visits',
                            'price' => 17000,
                            'studio_credits' => 30,
                        ])->getKey(),
                    ]);

                    if ($subscription = Subscription::find($order->orderable_id)) {
                        $subscription->delete();
                    }

                    $order->update([
                        'orderable_id' => $creditPackPurchase->id,
                        'orderable_type' => CreditPackPurchase::class,
                    ]);
                });
            }
        );
    }

    protected function createOrderForTenPackCreditGuests(
        array $data = [],
        int $want = null
    ): OrderCollection|Model {
        return tap(
            Order::factory()->forSubscription()->count($want)->create($data),
            function (OrderCollection $orders) {
                $orders->each(function (Order $order) {
                    $creditPackPurchase = CreditPackPurchase::factory()->create([
                        'order_id' => $order->id,
                        'credit_pack_id' => CreditPack::factory()->create([
                            'name' => '10 visits',
                            'price' => 17000,
                            'studio_credits' => 10,
                        ])->getKey(),
                    ]);

                    if ($subscription = Subscription::find($order->orderable_id)) {
                        $subscription->delete();
                    }

                    $order->update([
                        'orderable_id' => $creditPackPurchase->id,
                        'orderable_type' => CreditPackPurchase::class,
                    ]);
                });
            }
        );
    }

    protected function createOrderForOnePackCreditGuests(
        array $data = [],
        int $want = null
    ): OrderCollection|Model {
        return tap(
            Order::factory()->forSubscription()->count($want)->create($data),
            function (OrderCollection $orders) {
                $orders->each(function (Order $order) {
                    $creditPackPurchase = CreditPackPurchase::factory()->create([
                        'order_id' => $order->id,
                        'credit_pack_id' => CreditPack::factory()->create([
                            'name' => '1 session',
                            'price' => 10000,
                            'studio_credits' => 1,
                        ])->getKey(),
                    ]);

                    if ($subscription = Subscription::find($order->orderable_id)) {
                        $subscription->delete();
                    }

                    $order->update([
                        'orderable_id' => $creditPackPurchase->id,
                        'orderable_type' => CreditPackPurchase::class,
                    ]);
                });
            }
        );
    }

    protected function createOrderForIntroPackCreditGuests(
        array $data = [],
        int $want = null
    ): OrderCollection|Model {
        return tap(
            Order::factory()->forSubscription()->count($want)->create($data),
            function (OrderCollection $orders) {
                $orders->each(function (Order $order) {
                    $creditPackPurchase = CreditPackPurchase::factory()->create([
                        'order_id' => $order->id,
                        'credit_pack_id' => CreditPack::factory()->create([
                            'name' => 'Intro pack',
                            'price' => 3600,
                            'studio_credits' => 3,
                        ])->getKey(),
                    ]);

                    if ($subscription = Subscription::find($order->orderable_id)) {
                        $subscription->delete();
                    }

                    $order->update([
                        'orderable_id' => $creditPackPurchase->id,
                        'orderable_type' => CreditPackPurchase::class,
                    ]);
                });
            }
        );
    }

    protected function createOrderForNotRenewedSubscription(array $data = [], int $want = null): OrderCollection|Model
    {
        $orders = $this->createOrder($want, $data);

        $orders->each(fn (Order $order) => $order->orderable->update([
            'renew' => 0,
        ]));

        return $orders;
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    protected function createOrderForCreditPack(array $data = [], int $want = 1, ?CreditPack $creditPack = null)
    {
        return tap(
            Order::factory()->forSubscription()->count($want)->create($data),
            function (OrderCollection $orders) use ($creditPack) {
                $orders->each(function (Order $order) use ($creditPack) {

                    $creditPackPurchase = CreditPackPurchase::factory()->create([
                        'order_id' => $order->id,
                    ]);

                    if ($subscription = Subscription::find($order->orderable_id)) {
                        $subscription->delete();
                    }

                    $order->update([
                        'orderable_id' => $creditPackPurchase->id,
                        'orderable_type' => CreditPackPurchase::class,
                    ]);

                    if ($creditPack) {
                        $order->orderable->update([
                            'credit_pack_id' => $creditPack->id,
                        ]);
                    }
                });
            }
        );
    }

    protected function createOrderForCreditPackPurchase(
        int $order_id,
        array $data = [],
        int $want = null
    ): OrderCollection|Model {
        $orderable = CreditPackPurchase::factory()->create(compact('order_id'));

        return $this->createOrder($want, array_merge(
            [
                'orderable_id' => $orderable->id,
                'orderable_type' => get_class($orderable),
            ],
            $data
        ));
    }

    protected function createOrdersForPreviousNumberOfDays(int $numberOfDays, array $data = []): OrderCollection
    {
        return OrderCollection::make(range(1, $numberOfDays))
            ->map(function ($day, $index) use ($data) {
                return $this->createOrderForSubscription(array_merge(
                        $data,
                        [
                            'created_at' => $date = now()->subDays($index),
                            'updated_at' => $date,
                        ]
                    )
                );
            });
    }

    protected function setActualTime(int $year = 2022, int $month = 1, int $day = 10): void
    {
        Carbon::setTestNow(
            Carbon::create($year, $month, $day)
        );
    }

    protected function createOrder(
        ?int $want,
        array $data
    ): OrderCollection|Model {
        return Order::factory()
            ->forSubscription()
            ->count($want)
            ->create($data);
    }

    protected function createLead(
        ?int $want = null,
        array $data = []
    ): LeadCollection|Model {
        return Lead::factory()
            ->count($want)
            ->create($data);
    }

    /**
     * @param int|null $want
     * @param array $data
     * @return Collection|Model
     */
    protected function createSubscription(
        ?int $want = null,
        array $data = []
    ): Collection|Model {
        return Subscription::factory()
            ->count($want)
            ->create($data);
    }

    /**
     * @param int|null $want
     * @param array $data
     * @return Collection|Model
     */
    protected function createCreditPackPurchase(
        ?int $want = null,
        array $data = []
    ): Collection|Model {
        return CreditPackPurchase::factory()
            ->count($want)
            ->create($data);
    }

    /**
     * @param int|null $want
     * @param array $data
     * @return Collection|Model
     */
    protected function createCreditPack(
        ?int $want = null,
        array $data = []
    ): Collection|Model {
        return CreditPack::factory()
            ->count($want)
            ->create($data);
    }

    protected function createProduct(
        ?int $want = null,
        array $data = []
    ): Collection|Model {
        return Product::factory()
            ->count($want)
            ->create($data);
    }

    protected function createGym(
        ?int $want = null,
        array $data = []
    ): Collection|Model {
        return Gym::factory()
            ->count($want)
            ->create($data);
    }

    protected function createMember(
        ?int $want = null,
        array $data = []
    ): Collection|Member {
        return Member::factory()
            ->count($want)
            ->create($data);
    }

    protected function createReformerBooking(
        ?int $want = null,
        array $data = []
    ): Collection|Member {
        return ReformerBooking::factory()
            ->count($want)
            ->create($data);
    }

    protected function createDifferentTypesOfOrders(): void
    {
        $this->createOrderForNotRenewedSubscription([
            'promo_code' => 'random_promo_code',
        ], 25);
        $this->createOrderForRenewedSubscription([], 32);
        $this->createOrderForNotRenewedSubscription([], 99);
        $this->createOrderForCreditPack([], 216);
        $this->createOrderForVipSubscription([], 35);
    }
}
