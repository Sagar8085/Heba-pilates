<template>
    <div class="auth-subscription">
        <header class="header">
            <div class="wrapper">
                <router-link to="/login" class="header__burger__home">
                    <img src="/images/logos/heba-logo.png" alt="Heba Pilates" title="Heba Pilates">
                </router-link>
            </div>
        </header>

        <section class="auth-subscription__section auth-subscription__section--hero">
            <div class="wrapper">
                <p class="page-subtitle">Start your Heba Pilates journey here today</p>
                <h1 class="auth-subscription__section__title">Select a Subscription or Credit Pack</h1>
                <p class="auth-subscription__section__subtitle">Update or cancel your subscription at any time</p>
            </div>
        </section>

        <section v-if="loading || subscriptions.length == 0" class="auth-subscription__section">
            <loading-spinner class="wrapper" :loading="loading" loadingText="subscriptions" />
        </section>

        <template v-else>
            <section class="auth-subscription__section">
                <div class="wrapper">
                    <p class="page-subtitle">Getting Started</p>
                    <h2 class="auth-subscription__section__title">Introductory Offers</h2>

                    <div class="auth-subscription__section__cards">
                        <register-subscription-card
                            :subscription="subscriptions[0]"
                            @select="selectSubscription" />
                        <register-subscription-card
                            :subscription="subscriptions[1]"
                            @select="selectSubscription" />
                    </div>
                </div>
            </section>

            <section class="auth-subscription__section">
                <div class="wrapper">
                    <div class=" auth-subscription__subsection-container">
                        <div class="auth-subscription__subsection">
                            <p class="page-subtitle">Online Only</p>
                            <h2 class="auth-subscription__section__title">Online Memberships</h2>

                            <div class="auth-subscription__section__cards">
                                <register-subscription-card
                                    :subscription="subscriptions[2]"
                                    @select="selectSubscription" />
                            </div>
                        </div>

                        <div class="auth-subscription__subsection">
                            <p class="page-subtitle">Hybrid Subscriptions</p>
                            <h2 class="auth-subscription__section__title">Studio and Online Memberships</h2>

                            <div class="auth-subscription__section__cards">
                                <register-subscription-card
                                    :subscription="subscriptions[3]"
                                    @select="selectSubscription" />
                                <register-subscription-card
                                    :subscription="subscriptions[4]"
                                    @select="selectSubscription" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="auth-subscription__section">
                <div class="wrapper">
                    <div class=" auth-subscription__subsection-container">
                        <div class="auth-subscription__subsection">
                            <p class="page-subtitle">No Monthly Contract</p>
                            <h2 class="auth-subscription__section__title">One Off Memberships</h2>

                            <div class="auth-subscription__section__cards">
                                <register-subscription-card
                                    :subscription="subscriptions[5]"
                                    @select="selectSubscription" />
                            </div>
                        </div>

                        <div class="auth-subscription__subsection">
                            <p class="page-subtitle">Studio Credits</p>
                            <h2 class="auth-subscription__section__title">Studio Only Credit Packs</h2>

                            <div class="auth-subscription__section__cards">
                                <register-subscription-card
                                    :subscription="creditPacks[0]"
                                    @select="selectSubscription"
                                    isCreditPack />
                                <register-subscription-card
                                    :subscription="creditPacks[1]"
                                    @select="selectSubscription"
                                    isCreditPack />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </div>
</template>

<script>
import LoadingSpinner from '../layout/LoadingSpinner.vue'
import RegisterSubscriptionCard from './RegisterSubscriptionCard.vue'

export default {
    components: { RegisterSubscriptionCard, LoadingSpinner },
    data () {
        return {
            subscriptions: [
                {
                    id: 1,
                    price: 25,
                    payment_type: 'one-time',
                    name: 'Studio Drop-in',
                    description: 'One time in-studio visit',
                    studio_visits: 1,
                    price_id: 'price_1IYV3DKlaf0ZXKqlmTm1dRvK',
                    credit_pack_id: 2
                },
                {
                    id: 2,
                    price: 20,
                    payment_type: 'one-time',
                    name: 'Starter Session',
                    description: 'One time session credit',
                    studio_visits: 1,
                    price_id: 'price_1IYWe3Klaf0ZXKqlxHdYNoux',
                    credit_pack_id: 1
                },
                {
                    id: 3,
                    price: 45,
                    payment_type: 'monthly',
                    name: 'Online Only',
                    description: 'Unlimited On Demand and Live Classes',
                    studio_visits: 0,
                    price_id: 'price_1IYUmPKlaf0ZXKql9CWmeuhT',
                    identifier: 'online-only'
                },
                {
                    id: 4,
                    price: 99,
                    payment_type: 'monthly',
                    name: 'Mainly Home',
                    description: 'Unlimited On Demand and Live Classes',
                    studio_visits: 4,
                    visit_frequency: 'per month',
                    price_id: 'price_1IYUmsKlaf0ZXKqlIeU3PI6I',
                    identifier: 'mainly-home'
                },
                {
                    id: 5,
                    price: 149,
                    payment_type: 'monthly',
                    name: 'Mainly Studio',
                    description: 'Unlimited On Demand and Live Classes',
                    studio_visits: 9,
                    visit_frequency: 'per month',
                    price_id: 'price_1IYUn8Klaf0ZXKqlHrK5ewue',
                    identifier: 'mainly_studio'
                },
                {
                    id: 6,
                    price: 189,
                    payment_type: 'one-time',
                    name: 'One Month Standalone',
                    description: 'Unlimited On Demand and Live Classes',
                    studio_visits: 9,
                    visit_frequency: 'per month',
                    price_id: 'price_1IZFuWKlaf0ZXKqlt4cWhpMw',
                    credit_pack_id: 2
                }
            ],
            creditPacks: [
                {
                    id: 1,
                    price: 170,
                    payment_type: 'one-time',
                    name: '10 Pack',
                    description: '10 In-Studio Visits',
                    studio_visits: 10,
                    price_id: 'price_1IYV1mKlaf0ZXKql2fEXNM8S',
                    credit_pack_id: 3
                },
                {
                    id: 2,
                    price: 350,
                    payment_type: 'one-time',
                    name: '30 Pack',
                    description: '30 In-Studio Visits',
                    studio_visits: 30,
                    price_id: 'price_1IYV1mKlaf0ZXKqlN61xhUYo',
                    credit_pack_id: 4
                }
            ],
            loading: false
        }
    },
    methods: {
        selectSubscription (subscription) {
            this.$emit('select', subscription)
        }
    }
}
</script>
