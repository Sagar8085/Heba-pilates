<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content account__section">
            <div class="onboard__col__wrapper">
                <div class="subscriptions_onboard">
                    <h1 class="onboard__title">Subscriptions</h1>
                    <ul class="subscriptions__list">
                        <li class="subscriptions__list__item">
                            <subscription-card title="Online Only" price="£39" billingPeriod="Monthly" comingSoon :upgrading="this.upgrading" @upgrade="upgrade('online-only')">
                                <li>Unlimited @ Home On Demand Classes</li>
                                <li>Unlimited @ Home Live Classes</li>
                            </subscription-card>
                        </li>
                        <li class="subscriptions__list__item">
                            <subscription-card title="Premium" price="£125" billingPeriod="Monthly" showUpgrade :upgrading="this.upgrading" @upgrade="upgrade('premium-membership')">
                                <li>10 In-Studio Visits Per Month (worth £24 each)</li>
                                <!-- <li>Unlimited Heba at home on-demand at home access (worth £39pm)</li> -->
                                <li>Preferential advance booking allowance: 1 Month in advance</li>
                                <!-- <li>Saving of £130 compared to individual purchases</li> -->
                            </subscription-card>
                        </li>
                    </ul>
                    <ul class="subscriptions__list">
                        <li class="subscriptions__list__item">
                            <subscription-card title="Unlimited" price="£175" billingPeriod="Monthly" showUpgrade :upgrading="this.upgrading" @upgrade="upgrade('unlimited-membership')">
                                <li>Unlimited In-Studio Visits</li>
                                <!-- <li>Unlimited Heba at home on-demand at home access (worth £39pm)</li> -->
                                <li>Preferential advance booking allowance: 1 Month in advance</li>
                                <!-- <li>Saving of £130 compared to individual purchases</li> -->
                            </subscription-card>
                        </li>
                        <li class="subscriptions__list__item">
                            <subscription-card title="VIP Unlimited" price="£1,999" billingPeriod="Yearly" showUpgrade :upgrading="this.upgrading" @upgrade="upgrade('vip-unlimited')">
                                <li>Unlimited session allowance for full 12 month term of membership</li>
                                <li>Book anytime to suit your schedule</li>
                            </subscription-card>
                        </li>
                    </ul>
                </div>
                <div class="onboard__divider"></div>
                <h1 class="onboard__title">Membership Credit Packs</h1>

                <small style="font-size: .8rem; margin-bottom: 2rem; display: block">All of our credit packs can be used to visit our specially fitted Heba Pilates studios.</small>

                    <div class="row">
                        <div v-for="pack in creditPacks" :key="pack.id" class="columns twelve-xl">
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


                <stripe-checkout
                    ref="checkoutRef"
                    mode="payment"
                    :pk="publishableKey"
                    :line-items="lineItems"
                    :success-url="successURL"
                    :cancel-url="cancelURL"
                    @loading="v => loading = v"
                />
                <router-link style="margin-top: 2.5rem;" class="onboard__form__submit button button--full button--larger button--outline" to="/onboard/intro-pack">Back to Intro Pack</router-link>

                <router-link class="button button--full button--transparent" to="/onboard/focuses" style="margin-top: .5rem;">I'll skip for now</router-link>
            </div>
        </section>

        <section class="onboard__col onboard__col--image">
            <img src="/images/onboarding/onboarding--focuses.png" alt="Heba Pilates" draggable="false" />
        </section>
    </div>



</template>

<script>

import { StripeCheckout } from '@vue-stripe/vue-stripe';
import Modal from '../../../components/Modal.vue';
import SubscriptionCard from './OnBoardSubscriptionCard.vue'
import Payment from "../../../mixins/payment";

export default {
    components: { StripeCheckout, SubscriptionCard, Modal },

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

    methods: {
        upgrade(tier) {
            this.upgrading = true;

            axios.get('/api/account/subscription/stripe/bacs-checkout?tier=' + tier)
                .then(response => {
                    this.getPaymentHandler()
                        .redirectToCheckout({
                            sessionId: response.data.id
                        }).then(result => this.upgrading = false);
                })
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
                        }).then(function (result) {
                        // If `redirectToCheckout` fails due to a browser or network
                        // error, display the localized error message to your customer
                        // using `result.error.message`.
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
