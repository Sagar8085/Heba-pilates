<template>
<div class="landing">
    <section class="landing__section landing__section--hero">
        <div class="landing__section__container">
            <div class="landing__section__col">
                <span class="landing__section__subtitle">Workout From Home</span>
                <h1 class="landing__section__title">Heba Pilates brings you on-demand virtual training wherever you are</h1>
                <p class="landing__section__text">
                    On Demand Content and Live Class Workouts straight to your phone to carry out in your own time and within the comfort of your own home.
                </p>
                <span class="landing__section__subtitle" style="font-weight: bold">COMING SOON TO iOS and ANDROID</span>
            </div>
        </div>
    </section>
    <section class="landing__section landing__section--features">
        <div class="landing__section__container">
            <div class="landing__section__col landing__section__col--auto">
                <img src="/images/landing-page/iPhone-Mockup.png" alt="iPhone App" />
            </div>
            <div class="landing__section__col landing__section__col--center">
                <span class="landing__section__subtitle">Features</span>
                <h2 class="landing__section__title">Access professional podcast workouts and coaching videos on demand</h2>
                <ul class="landing__section__features">
                    <li v-for="feature in features" :key="feature.title" class="landing__section__feature">
                        <div class="landing__section__feature__image">
                            <img :src="`/images/landing-page/${feature.image}`" :alt="feature.title" />
                        </div>
                        <h3 class="landing__section__feature__title">{{ feature.title }}</h3>
                        <p class="landing__section__feature__description">{{ feature.description }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <section class="landing__section">
        <div class="landing__section__container">
            <div class="landing__section__col landing__section__col--center">
                <span class="landing__section__subtitle">Pricing</span>
                <h2 class="landing__section__title">Join us with a membership that suits you</h2>
                <ul class="subscriptions__list">
                    <li class="subscriptions__list__item">
                        <subscription-card title="Standard">
                            <li>FREE Live Classes</li>
                            <li>FREE On Demand Workouts</li>
                        </subscription-card>
                    </li>
                    <li class="subscriptions__list__item">
                        <subscription-card title="Premium" price="£55.99" active showUpgrade @upgrade="signUpWithSubscription('premium')">
                            <li>Everything from Standard Membership PLUS:</li>
                            <li>Access to all pay-per-view content</li>
                            <li>10 tokens per week to book paid live classes</li>
                        </subscription-card>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>
</template>

<script>
import SubscriptionCard from './account/AccountSubscriptionCard.vue'

export default {
    components: {
        SubscriptionCard
    },
    data () {
        return {
            features: [
                {
                    title: 'Live Classes',
                    description: 'Choose from a wide range of live class workouts based on your personal fitness & health goals.',
                    image: 'icon-workouts.png'
                },
                {
                    title: 'On Demand Classes',
                    description: 'Access a wide range of on demand videos led by our professional coaches.',
                    image: 'icon-ondemand.png'
                },
                {
                    title: 'Expert Advice',
                    description: 'Our coaches and classes contain world class advice from certified professionals',
                    image: 'icon-coaching.png'
                }
            ]
        }
    },
    methods: {
        signUpWithSubscription (subscription) {
            this.$router.push({ name: 'Register', query: { subscription }})
        }
    }
}
</script>
