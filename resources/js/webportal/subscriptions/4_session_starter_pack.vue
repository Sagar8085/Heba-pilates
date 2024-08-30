<template>
    <SubscriptionLandingPage :for-all-locations="true" :isLondon="isLondonStudio" title="4 session starter pack for £60" type="payment" id="19">
        <template slot="info">
            <p class="subscription-lp__title subscription-lp__title--light">
                Available at all studio locations (at this standard price of £60)
            </p>
            <p>Once payment is confirmed you will be able to complete your unique Heba profile account, book your first session and begin your Heba Journey.</p>
            <strong>Your credits will be active for 28 days from date of purchase  and will be live in your account as soon as you complete your profile account setup.</strong>

            <terms-and-conditions :isLondon="isLondonStudio" noteText="Your 28 days credit validity starts from date of purchase." />
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
            isLondonStudio: false
        }
    }
}
</script>
