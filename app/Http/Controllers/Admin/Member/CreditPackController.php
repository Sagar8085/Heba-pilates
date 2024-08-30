<?php

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\CallbackCreditPackRequest;
use App\Http\Requests\Member\StoreCreditPackRequest;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Event;
use App\Models\Gym;
use App\Models\Order;
use App\Models\User;
use App\Services\CheckoutSession;
use App\Services\Stripe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditPackController extends Controller
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

    /**
     * @param Gym $gym
     *
     * @return JsonResponse
     */
    public function allPacksForGym(Gym $gym): JsonResponse
    {
        return response()->json(
            CreditPack::byGym($gym)->get(),
        );
    }

    public function store(User $member, StoreCreditPackRequest $request)
    {
        $pack = $this->getPack($request);
        $purchase = tap($pack->generatePurchase($member))->save();

        Event::createForModel($request->event(), $purchase, $member->id);

        if ($request->prepaid()) {
            $order = $this->generateOrder($pack, $purchase);
            Event::createForModel($request->event(), $order, $member->id);
        }

        return response()->json([
            'status' => __('response.success'),
            'message' => __('pack.member.created'),
        ]);
    }

    /**
     * @param User $member
     * @param StoreCreditPackRequest $request
     *
     * @return JsonResponse
     */
    public function checkoutSession(User $member, StoreCreditPackRequest $request): JsonResponse
    {
        //Need to fix these messy stripe instantiations all over the place
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $pack = $this->getPack($request);
        $customer = $this->stripe->findOrCreateCustomer($member);
        $memberUrl = config('app.url') . '/admin/members/' . $member->id;

        $session = $this->checkoutSession
            ->setAllowsPromotionCodes()
            ->setMode(CheckoutSession::MODE_PAYMENT)
            ->setCustomer($customer->id)
            ->create([
                'payment_method_types' => [
                    CheckoutSession::PAYMENT_METHOD_CARD,
                ],
                'line_items' => [
                    [
                        'price' => $pack->getPriceForMember($member)->stripe_price_id,
                        'quantity' => 1,
                    ],
                ],
                'success_url' => $memberUrl . '/credit-packs/callback/success?session_id={CHECKOUT_SESSION_ID}&creditPackId=' . $pack->id,
                'cancel_url' => $memberUrl . '?credit-packs=purchase_cancelled',
            ]);

        return response()->json($session);
    }

    public function checkoutCallback(User $member, CallbackCreditPackRequest $request)
    {
        $pack = $this->getPack($request);
        $intent = $this->stripe->paymentIntentForSession($request->input('stripeSessionID'));

        if ($paymentMethod = $this->stripe->loadPaymentMethod($intent->payment_method)) {
            $member->updatePaymentMethod($paymentMethod);
        }

        $purchase = tap($pack->generatePurchase($member))->save();
        Event::createForModel($request->event(), $purchase, $member->id);

        $order = $this->generateOrder($pack, $purchase, $intent->id);
        Event::createForModel($request->event(), $order, $member->id);

        return response()->json([
            'status' => __('response.success'),
            'message' => __('pack.member.created'),
        ]);
    }

    protected function getPack(Request $request)
    {
        return CreditPack::findOrFail($request->input('creditPackId'));
    }

    protected function generateOrder(CreditPack $pack, CreditPackPurchase $purchase, $stripeOrderId = '')
    {
        return Order::create([
            'member_id' => auth()->id(),
            'value' => $pack->price,
            'method' => 'stripe',
            'orderable_id' => $purchase->id,
            'orderable_type' => CreditPackPurchase::class,
            'stripe_order_id' => $stripeOrderId,
        ]);
    }
}
