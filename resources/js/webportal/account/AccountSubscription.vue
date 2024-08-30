<template>
<div class="auth__wrapper">
    <back-navigation />
    <div class="wrapper subscriptions">
        <h1 class="subscriptions__title">Start your fitness journey</h1>
        <p class="subscriptions__text">Take the first step on your journey toward a healthier, happier life.</p>
        <ul class="subscriptions__list">
            <li class="subscriptions__list__item">
                <subscription-card title="Standard">
                    <li>x3 FREE workouts per category</li>
                    <li>x3 FREE On Demand classes per category</li>
                    <li>x3 FREE Podcasts per category</li>
                    <li>Access to Virtual Coaching</li>
                    <li>Access to Calorie Calculator</li>
                    <li>Access to all Meet the Experts content</li>
                </subscription-card>
            </li>
            <li class="subscriptions__list__item">
                <!-- <subscription-card title="Premium" price="£55.99" active showCurrent showUpgrade @upgrade="upgrade('premium')"> -->
                <subscription-card title="Premium" price="£55.99" active showUpgrade @upgrade="upgrade('premium')">
                    <li>Everything from Standard Membership and Podcast Training, PLUS:</li>
                    <li>Access to all pay-per-download content</li>
                </subscription-card>
            </li>
            <li class="subscriptions__list__item">
                <subscription-card title="Podcast Training" price="£13.99" showUpgrade @upgrade="upgrade('podcasts')">
                    <li>Everything from the Standard Membership</li>
                    <li>Unlock all Podcast training</li>
                </subscription-card>
            </li>
        </ul>
    </div>

    <stripe-checkout
        ref="checkoutRef"
        mode="subscription"
        :pk="publishableKey"
        :line-items="lineItems"
        :success-url="successURL"
        :cancel-url="cancelURL"
        @loading="v => loading = v"
    />
</div>
</template>
<script>
import { StripeCheckout } from '@vue-stripe/vue-stripe';
import BackNavigation from '../layout/BackNavigation.vue'
import SubscriptionCard from './AccountSubscriptionCard.vue'

export default {
    components: { BackNavigation, SubscriptionCard, StripeCheckout },

    data() {
        this.publishableKey = 'pk_test_7cnzJxhUjHPpUnNieEjE0GLX00tYinkqfY';

        return {
            lineItems: [
                {
                    price: 'price_1IHALfKhJPLSqczXmvCOsBLi', // The id of the one-time price you created in your Stripe dashboard
                    quantity: 1,
                },
            ],
            successURL: null,
            cancelURL: null,

            selectedClass: null,
            purchasing: false
        }
    },

    methods: {
        // You will be redirected to Stripe's secure checkout page
        upgrade (type) {
            this.purchasing = true;
            this.successURL = window.location.origin + '/subscription/purchase/' + type + '/success';
            this.cancelURL = window.location.origin + '/subscription/purchase/' + type + '/cancel';
            this.$refs.checkoutRef.redirectToCheckout();
        },
    },

    mounted () {
        if (this.$route.query.subscription)
            this.upgrade(this.$route.query.subscription)
    }
}
</script>
