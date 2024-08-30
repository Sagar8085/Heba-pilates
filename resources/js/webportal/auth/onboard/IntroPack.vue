<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content">
            <div class="onboard__col__wrapper">
                <h1 class="onboard__title">
                    New Guest Introductory Offer
                </h1>

                <p class="onboard__description">
                    Take advantage of our Intro Pack (one time offer available to new Guests), experience 2 weeks of
                    unlimited Nuforma
                    Pilates sessions and feel the amazing benefits of Heba Pilates.
                </p>

                <onboard-subscription
                    title="2 week unlimited offer"
                    :price="getPrice"
                    buttonText="Purchase Intro Pack"
                    @selected="buyCreditPack"
                >
                    <p class="onboard__subscription__description--primary">
                        ONE TIME OFFER! SECURE YOURS NOW
                    </p>
                </onboard-subscription>

                <p class="onboard__description" style="margin-top: 2.5rem;">
                    Intro Pack sessions are valid for 14 days
                    from date of purchase and can be used to book a session any time . Once you have purchased your
                    session pack and completed your health questionnaire, simply reserve your ideal time slot directly
                    within the app.
                </p>

                <router-link
                    style="margin-top: 2.5rem;"
                    class="onboard__form__submit button button--full button--larger button--outline"
                    to="/onboard/membership">See all memberships and credit packs
                </router-link>

                <router-link
                    class="button button--full button--transparent"
                    to="/onboard/focuses"
                    style="margin-top: .5rem;">I'll skip for now
                </router-link>

            </div>
        </section>
        <section class="onboard__col onboard__col--image">
            <img src="/images/onboarding/purchase-intro.jpg" alt="Heba Pilates" draggable="false"/>
        </section>
    </div>
</template>

<script>
import OnboardSubscription from './OnboardSubscription.vue'
import {StripeCheckout} from '@vue-stripe/vue-stripe';
import Payment from "../../../mixins/payment";

export default {
    components: {OnboardSubscription, StripeCheckout},
    mixins: [Payment],
    methods: {
        buyCreditPack() {
            axios.post('/api/account/credit-packs/11/checkout', {
                ref: '/onboard/focuses'
            }).then(response => {
                this.getPaymentHandler()
                    .redirectToCheckout({
                        sessionId: response.data.id
                    });
            });
        }
    },
    computed: {
        getPrice() {
            const user = this.$attrs.authUser;
            if (user.gym && user.gym.name === 'Clapham') {
                return '69.00';
            }
            return '49.00';
        }
    }
}
</script>
