<?php

namespace Tests\Http\Controllers\Api;

use App\Events\OrderCreatedExternally;
use App\Mail\SendEmail;
use App\Models\CreditPack;
use App\Models\User;
use Artisan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('heba:promo-credit-pack');
    }

    private function setHeaders(string $token = null)
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . ($token ?: config('api.token')),
        ]);
    }

    /** @test */
    public function itAuthorisesTheRequestWithToken(): void
    {
        $this->setHeaders('dodgy token')
            ->postJson(route('api.users.store'), [
                'first_name' => null,
                'last_name' => null,
                'email' => null,
                'phone_number' => null,
            ])
            ->assertForbidden();
    }

    /**
     * @test
     * @dataProvider payloads
     */
    public function itValidatesTheInput(array $payload): void
    {
        User::factory()->create([
            'email' => 'john@example-fudge.net'
        ]);

        $this->setHeaders()
            ->postJson(route('api.users.store'), $payload)
            ->assertUnprocessable();
    }

    public function payloads(): array
    {
        return [
            [
                [
                    'first_name' => null,
                    'last_name' => null,
                    'email' => null,
                    'phone_number' => null,
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'last_name' => null,
                    'email' => 'john@example',
                    'phone_number' => '012031234',
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'email' => 'smithy@example.com',
                    'phone_number' => null,
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'email' => null,
                    'phone_number' => '012031234',
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'email' => 'falsey email address',
                    'phone_number' => '012031234',
                ],
            ],
            [
                [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'email' => 'john@example-fudge.net',
                    'phone_number' => '012031234',
                ],
            ],
        ];
    }

    /** @test */
    public function itCreatesAUser(): void
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john@example.com',
            'phone_number' => '01204123456',
        ];

        $this->setHeaders()
            ->postJson(route('api.users.store'), $userData)
            ->assertOk();

        $user = User::first();
        $creditPack = CreditPack::query()
            ->where('name', 'Promo 4 sessions')
            ->first();

        $this->assertDatabaseHas('users', $userData);
        $this->assertDatabaseHas('credit_packs_purchases', [
            'user_id' => $user->id,
            'credit_pack_id' => $creditPack->id,
            'online_credits' => $creditPack->online_credits,
            'studio_credits' => $creditPack->studio_credits,
            'expires' => now()->addDays($creditPack->days_till_expiry),
        ]);
    }

    /** @test */
    public function itDispatchesAnEventWhenItCreatesTheUser(): void
    {
        Event::fake();

        $this->setHeaders()
            ->postJson(route('api.users.store'), [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john@example.com',
                'phone_number' => '01204123456',
            ])
            ->assertOk();

        Event::assertDispatched(OrderCreatedExternally::class);
    }

    /** @test */
    public function itSendsAMailableWhenItCreatesAUser(): void
    {
        Mail::fake();

        $this->setHeaders()
            ->postJson(route('api.users.store'), [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john@example.com',
                'phone_number' => '01204123456',
            ])
            ->assertOk();

        Mail::assertSent(SendEmail::class);
    }
}
