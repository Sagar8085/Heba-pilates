<template>
    <SubscriptionLandingPage :isLondon="isLondonStudio" title="2 weeks unlimited for £69" type="payment" id="11">
        <template slot="info">
            <p class="subscription-lp__title subscription-lp__title--light">
                One time only offer for new Heba Guests
            </p>
            <p>Once payment is confirmed you will be able to complete your unique Heba profile account, book your first session and begin your Heba Journey.</p>
            <strong>Your credits will be active for 14 days from date of purchase  and will be live in your account as soon as you complete your profile account setup.</strong>

            <terms-and-conditions :isLondon="isLondonStudio" noteText="Your 14 days credit validity starts from date of purchase." />
        </template>
    </SubscriptionLandingPage>
</template>

<script>
import SubscriptionLandingPage from './components/SubscriptionLandingPage.vue'
import TermsAndConditions from './components/TermsAndConditions.vue'

export default {
    components: { SubscriptionLandingPage, TermsAndConditions },

    data () {
        return {
            isLondonStudio: true
        }
    }
}
</script>
