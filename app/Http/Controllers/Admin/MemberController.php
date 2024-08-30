<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotification;
use App\Models\Contracts;
use App\Models\CreditPack;
use App\Models\CreditPackPurchase;
use App\Models\Event;
use App\Models\Focus;
use App\Models\Lead;
use App\Models\MarketingPreference;
use App\Models\MembersNotes;
use App\Models\Notification;
use App\Models\OnDemandView;
use App\Models\Order;
use App\Models\PARQ;
use App\Models\PivotUserTag;
use App\Models\ReformerBooking;
use App\Models\StripePaymentMethod;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use App\Models\User;
use App\Models\UserQRCode;
use App\Models\UserTag;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\StripeClient;

class MemberController extends Controller
{
    private function getStripeSecret()
    {
        return config('services.stripe.secret');
    }

    public function allNonPaginated()
    {
        return response()->json(User::onlyMembers()->get());
    }

    public function single(User $member)
    {
        $lead = Lead::where('user_id', $member->id)->first();
        $member->lead_id = $lead?->id;

        return response()->json(
            $member->load([
                'subscription',
                'memberProfile.homeStudio',
                'memberProfile.preferredStudio',
                'focuses',
                'notificationPreferences',
            ])
        );
    }

    public function update(User $member, Request $request)
    {
        $member->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        Lead::where('user_id', $member->id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        if (trim($request->new_password)) {
            $member->update([
                'password' => Hash::make($request->new_password),
            ]);
        }

        $member->memberProfile->update([
            'home_studio_id' => $request->home_studio_id,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function stats(User $member)
    {
        $stats = collect();

        $totalSpend = Order::sum('value');
        $totalMembers = User::onlyMembers()->count();
        $recentSpend = Order::where('member_id', $member->id)->latest()->first();

        $stats->push(
            [
                'name' => 'OD Classes Watched',
                'value' => OnDemandView::where('user_id', $member->id)->count(),
            ], [
            'name' => 'Live Classes Booked',
            'value' => 11,
        ], [
                'name' => 'In Studio Reservations',
                'value' => 16,
            ]
        );

        return response()->json($stats);
    }

    public function purchaseHistory(User $member)
    {
        return response()->json(
            Order::latest()->where('member_id', $member->id)->with('orderable')->paginate(25)
        );
    }

    public function sendPushNotification(User $member, Request $request)
    {
        SendPushNotification::dispatch($member, $request->title, $request->message)->onQueue('account-notifications');

        Notification::create([
            'recipient_id' => $member->id,
            'sender_id' => auth()->user()->id,
            'object_id' => 0,
            'type' => 'manual',
            'message' => $request->title . ' ' . $request->message,
            'read' => 0,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function notifications(User $member)
    {
        return response()->json(
            Notification::where('recipient_id', $member->id)
                ->with('sender')
                ->latest()
                ->paginate(15)
        );
    }

    public function allTags()
    {
        return response()->json(UserTag::get());
    }

    public function allFocuses()
    {
        return response()->json(Focus::get());
    }

    public function memberTags(User $member)
    {
        $tagIds = PivotUserTag::where('user_id', $member->id)->get()->pluck('tag_id')->toArray();
        $tags = UserTag::whereIn('id', $tagIds)->get();
        return response()->json($tags);
    }

    public function saveMemberTags(User $member, Request $request)
    {
        PivotUserTag::where('user_id', $member->id)->delete();

        foreach ($request->tags as $tag) {
            PivotUserTag::create([
                'user_id' => $member->id,
                'tag_id' => $tag['id'],
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function createNewTag(Request $request)
    {
        $slug = Str::slug($request->tag, '-');
        $tag = UserTag::where('slug', $slug)->first();

        if ($tag == null) {
            $tag = UserTag::create([
                'name' => $request->tag,
                'slug' => $slug,
            ]);
        }

        PivotUserTag::create([
            'user_id' => $request->user_id,
            'tag_id' => $tag->id,
        ]);

        return response()->json([
            'status' => 'success',
            'tag' => $tag,
        ]);
    }

    public function reservations(User $member, $type)
    {
        $reservations = ReformerBooking::latest()
            ->with('member', 'reformer', 'deleter')
            ->select('reformer_bookings.*', 'gyms.name as gym_name', 'gyms.id as gym_id')
            ->join('reformers', 'reformers.id', 'reformer_bookings.reformer_id')
            ->join('gyms', 'gyms.id', 'reformers.gym_id')
            ->where('reformer_bookings.user_id', $member->id);

        if ($type === 'recent') {
            $reservations = $reservations->take(5);
        }

        $reservations = $reservations->withTrashed()->get();

        return response()->json($reservations);
    }

    public function memberships(User $member, $type)
    {
        $memberships = Subscription::latest()->with('member')->where('user_id', $member->id);

        if ($type === 'recent') {
            $memberships = $memberships->take(5);
        }

        $memberships = $memberships->get();

        return response()->json($memberships);
    }

    public function creditPackPurchases(User $member, $type)
    {
        $creditPacks = CreditPackPurchase::latest()->with('pack', 'deleter')->where('user_id',
            $member->id)->withTrashed();

        if ($type === 'recent') {
            $creditPacks = $creditPacks->take(5);
        }

        $creditPacks = $creditPacks->get();

        return response()->json($creditPacks);
    }

    /**
     * Create a Stripe membership checkout session.
     * @param \App\Models\User $member
     * @param \Illuminate\Http\Request $request
     *
     * @return Json
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createMembershipCheckout(User $member, Request $request)
    {
        $subscriptionTier = SubscriptionTier::where('slug', $request->tier)->first();

        $customer = $this->fetchStripeCustomer($member);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['bacs_debit', 'card'],
            'line_items' => [
                [
                    'price' => $subscriptionTier->stripe_price_id,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'customer' => $customer->id,
            'allow_promotion_codes' => true,
            'success_url' => env('APP_URL') . '/admin/members/' . $member->id . '/membership/callback/success?session_id={CHECKOUT_SESSION_ID}&tier=' . $request->tier,
            'cancel_url' => env('APP_URL') . '/admin/members/' . $member->id . '?membership=purchase_cancelled',
        ]);

        return response()->json($session);
    }

    private function fetchStripeCustomer($member)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $customer = $stripe->customers->all(['email' => $member->email]);


        if (count($customer->data) === 0) {
            $customer = $stripe->customers->create([
                'name' => $member->name,
                'email' => $member->email,
            ]);
        } else {
            $customer = $customer->data[(count($customer) - 1)];
        }

        return $customer;
    }

    public function stripeMembershipCallbackResponse(User $member, Request $request)
    {
        $subscriptionTier = SubscriptionTier::where('slug', $request->tier)->first();

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->retrieve($request->stripeSessionID, []);

        $intent = $stripe->paymentIntents->retrieve($session->payment_intent, []);

        $paymentMethod = $intent->payment_method;

        $method = $stripe->paymentMethods->retrieve($paymentMethod, []);

        StripePaymentMethod::where('user_id', $member->id)->update(['default' => 0]);

        StripePaymentMethod::create([
            'user_id' => $member->id,
            'payment_method' => $paymentMethod,
            'type' => $method->type,
            'default' => 1,
        ]);

        $sub = Subscription::create([
            'user_id' => $member->id,
            'tier' => $request->tier,
            'expires' => Carbon::now()->addMonths(1)->format('Y-m-d H:i:s'),
            'renew' => 1,
            'online_credits' => $subscriptionTier->online_credits,
            'studio_credits' => $subscriptionTier->studio_credits,
            'stripe_id' => $paymentMethod,
            'stripe_payment_intent' => $session->payment_intent,
        ]);

        Event::create([
            'message' => 'Purchased Membership via Admin Panel',
            'user_id' => $member->id,
            'object_id' => $sub->id,
            'object_type' => 'App\Models\Subscription',
            'created_by' => auth()->user()->id,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }


    /**
     * Create a Stripe credit pack checkout session.
     * @param \App\Models\User $member
     * @param \Illuminate\Http\Request $request
     *
     * @return Json
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCreditPackCheckout(User $member, Request $request)
    {
        $creditPack = CreditPack::where('id', $request->creditPackId)->first();

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $creditPack->stripe_price_id,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'allow_promotion_codes' => true,
            'success_url' => env('APP_URL') . '/admin/members/' . $member->id . '/credit-packs/callback/success?session_id={CHECKOUT_SESSION_ID}&creditPackId=' . $request->creditPackId,
            'cancel_url' => env('APP_URL') . '/admin/members/' . $member->id . '?credit-packs=purchase_cancelled',
        ]);

        return response()->json($session);
    }

    public function stripeCreditPackCallbackResponse(User $member, Request $request)
    {
        $creditPack = CreditPack::where('id', $request->creditPackId)->first();

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->retrieve($request->stripeSessionID, []);

        $purchase = CreditPackPurchase::create([
            'user_id' => $member->id,
            'credit_pack_id' => $creditPack->id,
            'order_id' => 1,
            'online_credits' => $creditPack->online_credits,
            'studio_credits' => $creditPack->studio_credits,
            'expires' => Carbon::now()->addMonths(1),
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via Admin Panel',
            'user_id' => $member->id,
            'object_id' => $purchase->id,
            'object_type' => 'App\Models\CreditPackPurchase',
            'created_by' => auth()->user()->id,
        ]);

        $order = Order::create([
            'member_id' => $member->id,
            'value' => $creditPack->price,
            'method' => 'stripe',
            'orderable_id' => $purchase->id,
            'orderable_type' => 'App\Models\CreditPackPurchase',
            'stripe_order_id' => $session->payment_intent,
        ]);

        Event::create([
            'message' => 'Purchased Credit Pack via Admin Panel',
            'user_id' => $member->id,
            'object_id' => $order->id,
            'object_type' => 'App\Models\Order',
            'created_by' => auth()->user()->id,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function memberSearch(Request $request)
    {
        $keyword = $request->keyword;

        $users = User::where(function ($query) use ($keyword) {
            $query->where('first_name', 'like', '%' . $keyword . '%')
                ->orWhere('last_name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhereRaw("concat(first_name, ' ', last_name) like '%" . $keyword . "%' ");
        })->with('subscription')->get();

        return response()->json($users);
    }

    public function memberUpgrade(Request $request)
    {
        $users = User::where('id', $request->id)->update(['role_id' => '1']);

        return response()->json($users);
    }

    public function storeMembership(User $member, Request $request)
    {
        $tier = SubscriptionTier::where('slug', $request->tier)->first();

        $sub = Subscription::create([
            'user_id' => $member->id,
            'tier' => $request->tier,
            'expires' => Carbon::now()->addMonths(1),
            'renew' => 0,
            'online_credits' => $tier->online_credits,
            'studio_credits' => $tier->studio_credits,
        ]);


        if ($request->type === 'already-paid') {
            Event::create([
                'message' => 'Already-Paid Membership added via Admin Panel',
                'user_id' => $member->id,
                'object_id' => $sub->id,
                'object_type' => 'App\Models\Subscription',
                'created_by' => auth()->user()->id,
            ]);

            $order = Order::create([
                'member_id' => $member->id,
                'value' => $tier->price,
                'method' => 'stripe',
                'orderable_id' => $sub->id,
                'orderable_type' => 'App\Models\Subscription',
                'stripe_order_id' => 0,
            ]);

            Event::create([
                'message' => 'Already-Paid Membership added via Admin Panel',
                'user_id' => $member->id,
                'object_id' => $order->id,
                'object_type' => 'App\Models\Order',
                'created_by' => auth()->user()->id,
            ]);
        } else {
            Event::create([
                'message' => 'Free Membership Added via Admin Panel',
                'user_id' => $member->id,
                'object_id' => $sub->id,
                'object_type' => 'App\Models\Subscription',
                'created_by' => auth()->user()->id,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function storeCreditPack(User $member, Request $request)
    {
        $pack = CreditPack::where('id', $request->creditPackId)->first();

        $purchase = CreditPackPurchase::create([
            'user_id' => $member->id,
            'credit_pack_id' => $pack->id,
            'online_credits' => $pack->online_credits,
            'studio_credits' => $pack->studio_credits,
            'expires' => $pack->days_till_expiry !== null ? Carbon::now()->addDays($pack->days_till_expiry) : ($pack->months_till_expiry !== null ? Carbon::now()->addMonths($pack->months_till_expiry) : null),
        ]);

        if ($request->purchaseType === 'prepaid') {
            Event::create([
                'message' => auth()->user()->name . ' added a Prepaid Credit Pack',
                'user_id' => $member->id,
                'object_id' => $purchase->id,
                'object_type' => 'App\Models\CreditPackPurchase',
                'created_by' => auth()->user()->id,
            ]);

            $order = Order::create([
                'member_id' => auth()->user()->id,
                'value' => $pack->price,
                'method' => 'stripe',
                'orderable_id' => $purchase->id,
                'orderable_type' => 'App\Models\CreditPackPurchase',
            ]);

            Event::create([
                'message' => auth()->user()->name . ' added a Prepaid Credit Pack',
                'user_id' => auth()->user()->id,
                'object_id' => $order->id,
                'object_type' => 'App\Models\Order',
                'created_by' => auth()->user()->id,
            ]);
        } else {
            Event::create([
                'message' => 'Free Credit Pack Added',
                'user_id' => $member->id,
                'object_id' => $purchase->id,
                'object_type' => 'App\Models\CreditPackPurchase',
                'created_by' => auth()->user()->id,
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function deleteMembership(User $member, Subscription $membership)
    {
        $membership->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function deleteCreditPack(User $member, CreditPackPurchase $creditPackPurchase)
    {
        $creditPackPurchase->update([
            'deleted_by' => auth()->user()->id,
        ]);
        $creditPackPurchase->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function updateMarketingPreference(User $member, Request $request)
    {
        $preference = MarketingPreference::where('member_id', $member->id)->first();

        if ($preference === null) {
            $preference = MarketingPreference::create([
                'member_id' => $member->id,
            ]);
        }

        $id = $request->id;

        if ($preference->{$id} === 1) {
            $newValue = 0;
        } else {
            $newValue = 1;
        }

        $preference->update([
            "$id" => $newValue,
        ]);

        return response()->json($preference);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'send_email' => 'required',
        ]);

        $user = User::create([
            'role_id' => 4,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make(\Str::random(100))
            // A random string is assigned to this password as the recipient will recieve an email to confirm account and set a password
        ]);
        if ($$request->send_email == 'yes') {
            $user->sendInvitationEmail();
        }


        return response()->json('done');
    }

    public function accessLog(User $member)
    {
        $accessLog = UserQRCode::where('user_id', $member->id)->orderBy('scanned_at',
            'DESC')->whereNotNull('scanned_at')->take(10)->with('user.subscription')->get();

        return response()->json($accessLog);
    }

    public function listContracts(Request $request, User $member): JsonResponse
    {
        $contract = [];
        $contracts = Contracts::where('user_id', $member->id)->get();

        foreach ($contracts as $val) {
            $newContract = array();
            $author = User::find($val->created_by);
            $newContract['contract'] = $val;
            $newContract['author'] = $author;
            $newContract['file_path'] = getenv('AWS_BUCKET_URL') . '/' . $val->path;
            array_push($contract, $newContract);
        }


        return response()->json($contract);
    }

    public function getNotes(User $user)
    {
        $notes = [];
        $memberNotes = MembersNotes::where('user_id', $user->id)->latest()->get();

        foreach ($memberNotes as $note) {
            $username = User::select('first_name', 'last_name')->where('id', $note->created_by)->get();
            $newNote = array(
                'author' => $username[0]->first_name . ' ' . $username[0]->last_name,
                'content' => $note->content,
                'created_at_human' => $note->created_at_human,
            );
            //$notes[]['author'] = $username[0]->first_name.' '.$username[0]->last_name;
            //$notes[]['content'] = $note->content;
            // $notes[]['created_at_human'] = $note->created_at_human;
            array_push($notes, $newNote);


        }

        return response()->json($notes);
    }

    public function saveNotes(Request $request, User $user)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $current = auth()->user();

        $addNote = MembersNotes::create([
            'user_id' => $user->id,
            'created_by' => $current->id,
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'status' => 'success',
            'note' => $addNote,
        ]);
    }

    public function loadParqs(User $user)
    {
        $parqs_response = array();
        $parqs_array = array();
        $parqs = PARQ::where('user_id', $user->id)->get();

        foreach ($parqs as $parq) {
            $createdBy = User::select('first_name', 'last_name')->where('id', $parq->created_by)->get();
            $parqs_response['author'] = $createdBy[0]->first_name . ' ' . $createdBy[0]->last_name;
            $parqs_response['parq'] = $parq;
            array_push($parqs_array, $parqs_response);
        }
        return response()->json($parqs_array);
    }

    public function createParqs(Request $request, User $user)
    {
        $current = auth()->user();
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'current_injuries' => 'required',
            'taking_medication' => 'required',
            'advised_by_doctor' => 'required',
            'currently_pregnant' => 'required',
            'current_injuries_details' => 'required_if:current_injuries,yes',
            'taking_medication_details' => 'required_if:taking_medication,yes',
            'advised_by_doctor_details' => 'required_if:advised_by_doctor,yes',
            'currently_pregnant_details' => 'required_if:currently_pregnant,yes',
        ]);
        $newParq = PARQ::create([
            'user_id' => $user->id,
            'created_by' => $current->id,
            'current_injuries' => $request->current_injuries === 'yes' ? 1 : 0,
            'taking_medication' => $request->taking_medication === 'yes' ? 1 : 0,
            'advised_by_doctor' => $request->advised_by_doctor === 'yes' ? 1 : 0,
            'currently_pregnant' => $request->currently_pregnant === 'yes' ? 1 : 0,
            'contact_first_name' => $request->first_name,
            'contact_last_name' => $request->last_name,
            'contact_phone_number' => $request->phone_number,
            'contact_email' => $request->email,
            'current_injuries_details' => $request->current_injuries_details,
            'taking_medication_details' => $request->taking_medication_details,
            'advised_by_doctor_details' => $request->advised_by_doctor_details,
            'currently_pregnant_details' => $request->currently_pregnant_details,
        ]);


        return response()->json($newParq);

    }


    public function deleteParqs(Request $request, User $user)
    {
        $newParq = PARQ::where('id', $request->id)->delete();

        return response()->json('ok');

    }

    public function createLeadProfile(User $member)
    {
        Lead::create([
            'user_id' => $member->id,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'email' => $member->email,
            'phone_number' => $member->phone_number,
        ]);

        return response()->json('', 200);
    }
}
