<template>
    <AccountPageWrapper class="membership">
        <section class="account__section">
            <h1>Memberships and Credit Packs</h1>

<!-- -------------------------------------------------------------------------------------------- -->
            <div class="account-info">
                <span class="account-info__title">{{this.authUser.gym.name}}</span>
                <router-link class="account-info__button" to="/membership_studio/change">Change Studio</router-link>
                <!-- <button v-on:click="handler">checkkkk</button> -->
            </div>
<!-- -------------------------------------------------------------------------------------------- -->
            <div v-if="this.authUser.subscription !== null">
                <!-- <h3 class="page-subtitle">Active Membership - <a href="javascript: void(0);" style="text-transform: none;" @click="displayDirectDebitDetailsModal = true">&nbsp;Update Direct Debit Details</a></h3> -->
                <h3 class="page-subtitle">Active Membership</h3>
                <ul class="booking-list">
                    <li class="booking-list__booking horizontal-card--bordered horizontal-card--border-orange">
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">Type</h4>
                            <p class="booking-list__booking__section__value booking-list__booking__section__value--large">
                                {{ this.authUser.subscription.name }}
                            </p>
                        </div>
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">Online Credits</h4>
                            <p class="booking-list__booking__section__value">{{ this.authUser.subscription.online_credits_human }}</p>
                        </div>
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">In-Studio Credits</h4>
                            <p class="booking-list__booking__section__value">{{ this.authUser.subscription.studio_credits_human }} Remaining</p>
                        </div>
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">{{ this.authUser.subscription.renew ? 'Renews' : 'Expires' }}</h4>
                            <p class="booking-list__booking__section__value">{{ this.authUser.subscription.expires_human }}</p>
                        </div>
                        <div class="booking-list__booking__section">
                            <button class="button button--outline" @click="manageSubscriptionModal = true">Manage</button>
                        </div>
                    </li>
                </ul>
            </div>

            <div v-else-if="this.pausedMembership !== null && this.pausedMembership.id != null">
                <h3 class="page-subtitle">Paused Membership</h3>
                <small style="font-size: .8rem;">You can un-pause your membership at any time. The amount of 'remaining days' is how many days you will have left of your subscription until it expires, once it is un-paused. For example; if you un-pause your membership on the 12th of July with 8 remaining days. Your subscription will expire on the 20th of July.</small>
                <ul class="booking-list">
                    <li class="booking-list__booking horizontal-card--bordered horizontal-card--border-orange">
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">Type</h4>
                            <p class="booking-list__booking__section__value booking-list__booking__section__value--large">
                                {{ this.pausedMembership.name }}
                            </p>
                        </div>
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">Online Credits</h4>
                            <p class="booking-list__booking__section__value">{{ this.pausedMembership.online_credits_human }}</p>
                        </div>
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">In-Studio Credits</h4>
                            <p class="booking-list__booking__section__value">{{ this.pausedMembership.studio_credits_human }}</p>
                        </div>
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">Remaining Days</h4>
                            <p class="booking-list__booking__section__value">{{ this.pausedMembership.pause_days }}</p>
                        </div>
                        <div class="booking-list__booking__section">
                            <button class="button button--outline" @click="unpauseMembership">Unpause</button>
                        </div>
                    </li>
                </ul>
            </div>

            <div v-else>
                You do not currently have a membership to Heba Pilates.
                <div class="subscriptions">
                    <ul class="subscriptions__list">
                        <li class="subscriptions__list__item">
                            <subscription-card title="Once Weekly" :price="`£${this.membershipPrices['once-weekly']}`" billingPeriod="Monthly" showUpgrade :upgrading="this.upgrading" @upgrade="upgrade('once-weekly')">
                                <li>4 In-Studio Visits Per Month (worth £{{ singleSessionPrice }} each)</li>
                            </subscription-card>
                        </li>
                        <li class="subscriptions__list__item">
                            <subscription-card title="Twice Weekly" :price="`£${this.membershipPrices['twice-weekly']}`" billingPeriod="Monthly" showUpgrade :upgrading="this.upgrading" @upgrade="upgrade('twice-weekly')">
                                <li>8 In-Studio Visits Per Month (worth £{{ singleSessionPrice }} each)</li>
                            </subscription-card>
                        </li>
                        <li class="subscriptions__list__item">
                            <subscription-card title="Unlimited" :price="`£${this.membershipPrices['unlimited-membership-2']}`" billingPeriod="Monthly" showUpgrade :upgrading="this.upgrading" @upgrade="upgrade('unlimited-membership-2')">
                                <li>Unlimited In-Studio Visits</li>
                                <li>1 Guest Pass per month</li>
                            </subscription-card>
                        </li>
                        <li class="subscriptions__list__item">
                            <subscription-card title="Unlimited Annual" :price="`£${this.membershipPrices['vip-unlimited-2']}`" billingPeriod="Yearly" showUpgrade :upgrading="this.upgrading" @upgrade="upgrade('vip-unlimited-2')">
                                <li>Unlimited session allowance for full 12 month term of membership</li>
                                <li>Book anytime to suit your schedule</li>
                            </subscription-card>
                        </li>
                    </ul>
                </div>
                <!-- <br><br>
                <router-link class="button button--blue button--full" to="/myaccount/subscription">View Plans</router-link> -->
            </div>
        </section>

        <section class="account__section membership__credit-packs">
            <h3 class="page-subtitle">My Credit Packs</h3>

            <ul class="booking-list" v-if="this.creditPackPurchases.length > 0">
                <li class="booking-list__booking horizontal-card--bordered horizontal-card--border-green" v-for="(creditPack, index) in this.creditPackPurchases" :key="index">
                    <div class="booking-list__booking__section">
                        <h4 class="page-subtitle">Type</h4>
                        <p class="booking-list__booking__section__value booking-list__booking__section__value--large">{{ creditPack.pack.name }}</p>
                    </div>
                    <div class="booking-list__booking__section">
                        <h4 class="page-subtitle">In-Studio Credits</h4>
                        <p class="booking-list__booking__section__value">{{ creditPack.studio_credits }} / {{ creditPack.pack.studio_credits }} Remaining</p>
                    </div>
                    <div class="booking-list__booking__section">
                    </div>
                </li>
            </ul>

            <ul v-else>
                You have not purchased any additional credit packs yet.
            </ul>
        </section>

        <section class="account__section membership__credit-packs">
            <h3 class="page-subtitle">Buy Credit Packs</h3>
            <small style="font-size: .8rem; margin-bottom: 2rem; display: block">All of our credit packs can be used to visit our specially fitted Heba Pilates studios.</small>

            <div class="row">
                <div v-for="pack in creditPacks" :key="pack.id" class="columns six-xl">
                    <div class="horizontal-card horizontal-card--3-cols">
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Type</label>
                            <p class="horizontal-card__section__value horizontal-card__section__value--large">
                                {{ pack.name }}
                                <br>
                                <small style="font-size: .75rem; font-weight: 400;">Includes {{ pack.studio_credits }} Studio Passes</small>
                            </p>
                        </div>
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Price</label>
                            <p class="horizontal-card__section__value horizontal-card__section__value--large">
                                £{{ pack.price_human }}
                            </p>
                        </div>
                        <div class="horizontal-card__section">
                            <button class="button" @click="buyCreditPack(pack)" v-if="!purchasing">Buy</button>
                            <button class="button" v-if="purchasing" disabled><i class="fas fa-spinner fa-spin"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <stripe-checkout
            ref="checkoutRef"
            mode="payment"
            :pk="publishableKey"
            :line-items="lineItems"
            :success-url="successURL"
            :cancel-url="cancelURL"
            @loading="v => loading = v"
        />

        <Modal v-model="manageSubscriptionModal" title="Manage Subscription" hideCancel @close="manageSubscriptionModal = false">
            How would you like to manage your Heba Pilates subscription?
            <template slot="buttons">
                <button class="button button--outline button--full" @click="manageSubscriptionModal = false">Close</button>
                <button class="button button--full" @click="manageSubscriptionModal = false; confirmCancelModal = true;">Cancel Subscription</button>
                <button class="button button--full" @click="manageSubscriptionModal = false; pauseMembershipModal = true;">Pause Membership</button>
                <!-- <button class="button button--full" @click="">Edit Direct Debit</button> -->
            </template>
        </Modal>

        <Modal v-model="confirmCancelModal" title="Cancel Your Membership" hideCancel @close="confirmCancelModal = false">
            Are you sure you wish to cancel your Heba Pilates membership? You will have regular access to you subscription until your expiry date.
            <template slot="buttons">
                <button class="button button--outline button--full" @click="confirmCancelModal = false">Nevermind</button>
                <button class="button button--full" @click="cancelSubscription">Cancel Subscription</button>
            </template>
        </Modal>

        <Modal v-model="subscriptionCancelled" title="Membership Cancelled" hideCancel @close="subscriptionCancelled = false">
            Your membership has been cancelled. You will have regular access to you subscription until your expiry date.
            <template slot="buttons">
                <button class="button button--outline button--full" @click="subscriptionCancelled = false">Okay</button>
            </template>
        </Modal>

        <Modal v-model="membershipPausedModal" title="Membership Paused" hideCancel @close="membershipPausedModal = false">
            Your Heba Pilates membership has now been paused, this can be resumed at anytime from within your Membership area.
            <template slot="buttons">
                <a href="/membership" class="button button--outline button--full">Okay</a>
            </template>
        </Modal>

        <Modal v-model="pauseMembershipModal" title="Pause your Membership" hideCancel @close="pauseMembershipModal = false">
            If you pause your membership, you will immediately loose access to all of it's benefits. However your remaining days will be added back to your account when you resume. For example, if you have 12 days left on the date you pause, when you resume you will recieve 12 days of membership.
            <template slot="buttons">
                <button class="button button--outline button--full" @click="pauseMembershipModal = false">Cancel</button>
                <button class="button button--full" @click="pauseMembership">Okay, Pause</button>
            </template>
        </Modal>

        <Modal v-model="displayDirectDebitDetailsModal" title="Direct Debit Details" hideCancel @close="displayDirectDebitDetailsModal = false">
            Please enter your new direct debit details below.

            <p v-if="bacsError !== ''" style="color: red">{{ bacsError }}</p>

            <div class="row">
                <div class="six columns" style="margin-bottom: 0">
                    <div class="form-element">
                        <span class="form-element__label">
                            * Sort Code E.g: (108800)
                            <span v-if="this.bacsErrors['sortCode']">{{ this.bacsErrors['sortCode'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="tel" required v-model="bacs_sort_code" placeholder="108800">
                        </div>
                    </div>
                </div>

                <div class="six columns" style="margin-bottom: 0">
                    <div class="form-element">
                        <span class="form-element__label">
                            * Account Number
                            <span v-if="this.bacsErrors['accountNumber']">{{ this.bacsErrors['accountNumber'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="tel" required v-model="bacs_account_number" placeholder="12345678">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="twelve columns" style="margin-bottom: 0">
                    <div class="form-element">
                        <span class="form-element__label">
                            * Billing Address Line 1
                            <span v-if="this.bacsErrors['addressOne']">{{ this.bacsErrors['addressOne'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="text" required v-model="bacs_address_one" placeholder="1 Newton Street">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="six columns" style="margin-bottom: 0">
                    <div class="form-element">
                        <span class="form-element__label">
                            * Billing City
                            <span v-if="this.bacsErrors['city']">{{ this.bacsErrors['city'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="text" required v-model="bacs_city" placeholder="London">
                        </div>
                    </div>
                </div>

                <div class="six columns" style="margin-bottom: 0">
                    <div class="form-element">
                        <span class="form-element__label">
                            * Billing Postcode
                            <span v-if="this.bacsErrors['postCode']">{{ this.bacsErrors['postCode'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="text" required v-model="bacs_postcode" placeholder="LA1 1AB">
                        </div>
                    </div>
                </div>
            </div>

            <template slot="buttons">
                <button class="button button--outline button--full" @click="displayDirectDebitDetailsModal = false">Cancel</button>
                <button class="button button--full" @click="updateDirectDebitDetails">Confirm</button>
            </template>
        </Modal>

    </AccountPageWrapper>
</template>

<script>
import AccountPageWrapper from './AccountPageWrapper.vue';
import SubscriptionCard from './AccountSubscriptionCard.vue'
import { StripeCheckout } from '@vue-stripe/vue-stripe';
import Modal from '../../components/Modal.vue';
import Payment from "../../mixins/payment";

export default {
    components: { AccountPageWrapper, SubscriptionCard, StripeCheckout, Modal },

    mixins: [Payment],

    props: { authUser: Object },

    mounted() {
        this.loadCreditPacks();
        this.loadCreditPackPurchases();
        this.loadPausedMembership();
    },

    data() {
        this.publishableKey = 'pk_live_51IQzRJKlaf0ZXKqlbjV8UWOnOYo9N4qK7jpMYdxTQYEizMj8ukytE2mt5O7kLgHcquqzxrPZcJzpaJ9jQgpa1U0C00iw9FPCGH';

        return {
            loading: true,
            purchasing: false,
            creditPacks: [],

            lineItems: [],
            successURL: '',
            cancelURL: '',

            creditPackPurchases: [],
            manageSubscriptionModal: false,
            subscriptionCancelled: false,
            confirmCancelModal: false,
            displayDirectDebitDetailsModal: false,
            pauseMembershipModal: false,
            membershipPausedModal: false,

            bacsErrors: [],
            bacs_sort_code: '',
            bacs_account_number: '',
            bacs_address_one: '',
            bacs_city: '',
            bacs_postcode: '',
            bacsError: '',

            pausedMembership: {},
            upgrading: false
        }
    },

    computed: {
        // Hack, hack, hack. The original prices were hardcoded, and I don't have
        // time to write something to load them in from the API, so I'm just
        // hardcoding the London prices too.
        // @FIXME
        membershipPrices() {
            if (this.authUser.gym.name === 'Clapham') {
                return {
                    'once-weekly': '90',
                    'twice-weekly': '140',
                    'unlimited-membership-2': '180',
                    'vip-unlimited-2': '1,800',
                };
            }

            return {
                'once-weekly': '70',
                'twice-weekly': '110',
                'unlimited-membership-2': '155',
                'vip-unlimited-2': '1,700',
            };
        },

        // @FIXME
        singleSessionPrice() {
            if (this.authUser.gym.name === 'Clapham') {
                return '30';
            }

            return '24';
        },
    },

    methods: {
        upgrade(tier) {
            this.upgrading = true;
            console.log('generating stripe checkout');

            axios.get('/api/account/subscription/stripe/bacs-checkout?tier=' + tier)
                .then(response => {
                    this.getPaymentHandler()
                        .redirectToCheckout({
                            sessionId: response.data.id
                        }).then(function (result) {
                        this.upgrading = false;
                        // If `redirectToCheckout` fails due to a browser or network
                        // error, display the localized error message to your customer
                        // using `result.error.message`.
                    });
                })
        },


   handler()
        {
            console.log("Memberships and Credit Packs authUser>>>>>>",this.authUser)
        },


        /*
         * Load all available credit packs for purchase.
         * @param {none}
         */
        loadCreditPacks() {
            axios.get('/api/credit-packs').then(response => {
                this.creditPacks = response.data;
                this.loading = false;
            });
        },

        /*
         * Load all credit packs this user has purchased.
         * @param {none}
         */
        loadCreditPackPurchases() {
            axios.get('/api/account/my-credit-packs').then(response => {
                this.creditPackPurchases = response.data;
            });
        },

        buyCreditPack(creditPack) {
            this.purchasing = true;
            console.log('generating checkout');

            axios.post('/api/account/credit-packs/' + creditPack.id + '/checkout')
                .then(response => {
                    this.getPaymentHandler()
                        .redirectToCheckout({
                            sessionId: response.data.id
                        });
                });
        },

        cancelSubscription() {
            axios.patch('/api/account/subscription/cancel').then(response => {
                this.confirmCancelModal = false;
                this.subscriptionCancelled = true;
            });
        },

        updateDirectDebitDetails() {
            axios.post('/api/account/bacs/update', {
                sortCode: this.bacs_sort_code,
                accountNumber: this.bacs_account_number,
                addressOne: this.bacs_address_one,
                city: this.bacs_city,
                postCode: this.bacs_postcode
            }).then(response => {
                console.log('has response')
                console.log(response.data)
            }).catch(error => {
                this.bacsError = error.response.data.message;
                this.bacsErrors = error.response.data.errors;
            });
        },

        loadPausedMembership() {
            axios.get('/api/account/membership/paused').then(response => {
                this.pausedMembership = response.data;
            });
        },

        pauseMembership() {
            axios.post('/api/account/membership/pause').then(response => {
                console.log('membership paused!')
                this.pauseMembershipModal = false;
                this.membershipPausedModal = true;
            });
        },

        unpauseMembership() {
            axios.post('/api/account/membership/unpause', {
                subscriptionId: this.pausedMembership.id
            }).then(response => {
                window.location.reload();
            });
        }
    }
}
</script>
