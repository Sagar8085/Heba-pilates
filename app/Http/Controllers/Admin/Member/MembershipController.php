<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\CallbackMembershipRequest;
use App\Http\Requests\Member\StoreMembershipRequest;
use App\Models\Event;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use App\Models\User;
use App\Services\CheckoutSession;
use App\Services\Stripe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    /**
     * @var Stripe
     */
    protected $stripe;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    public function __construct(Stripe $stripe, CheckoutSession $checkoutSession)
    {
        $this->stripe = $stripe;
        $this->checkoutSession = $checkoutSession;
    }

    public function store(User $member, StoreMembershipRequest $request)
    {
        $tier = $this->getTier($request);
        $subscription = tap($tier->memberSubscription($member))->save();

        Event::createForModel($request->event(), $subscription, $member->id);

        if ($request->hasAlreadyPaid()) {
            $order = Order::create([
                'member_id' => $member->id,
                'value' => $tier->price,
                'method' => Order::METHOD_STRIPE,
                'orderable_id' => $subscription->id,
                'orderable_type' => Subscription::class,
                'stripe_order_id' => 0,
            ]);

            Event::createForModel($request->event(), $order, $member->id);
        }

        return response()->json([
            'status' => __('response.success'),
            'message' => __('membership.member.created'),
        ]);
    }

    /**
     * @param User $member
     * @param StoreMembershipRequest $request
     *
     * @return JsonResponse
     */
    public function checkoutSession(User $member, StoreMembershipRequest $request): JsonResponse
    {
        $tier = $this->getTier($request);
        $customer = $this->stripe->findOrCreateCustomer($member);
        $memberUrl = config('app.url') . '/admin/members/' . $member->id;

        $session = $this->checkoutSession
            ->setAllowsPromotionCodes()
            ->setMode(CheckoutSession::MODE_SUBSCRIPTION)
            ->setCustomer($customer->id)
            ->create([
                'line_items' => [
                    [
                        'price' => $tier->getPriceForMember($member)->stripe_price_id,
                        'quantity' => 1,
                    ],
                ],
                'success_url' => $memberUrl . '/membership/callback/success?session_id={CHECKOUT_SESSION_ID}&tier=' . $tier->slug,
                'cancel_url' => $memberUrl . '?membership=purchase_cancelled',
            ]);

        return response()->json($session);
    }

    public function checkoutCallback(User $member, CallbackMembershipRequest $request)
    {
        $tier = $this->getTier($request);
        $intent = $this->stripe->paymentIntentForSession($request->input('stripeSessionID'));

        $subscription = $tier->memberSubscription($member, true)
            ->setAttribute('stripe_id', $intent->payment_method)
            ->setAttribute('stripe_payment_intent', $intent->id);
        $subscription->save();

        Event::createForModel($request->event(), $subscription, $member->id);

        return response()->json([
            'status' => __('response.success'),
            'message' => __('membership.member.created'),
        ]);
    }

    protected function getTier(Request $request)
    {
        return SubscriptionTier::where('slug', $request->input('tier'))->firstOrFail();
    }
}
