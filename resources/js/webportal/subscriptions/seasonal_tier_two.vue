<template>
    <SubscriptionLandingPage :for-all-locations="false" :isLondon="false" title="Seasonal Offer for £330" type="payment" id="20">
        <template slot="info">
            <p class="subscription-lp__title subscription-lp__title--light">
                Available at all studio locations for £330
            </p>
            <p>Once payment is confirmed you will be able to complete your unique Heba profile account, book your first session and begin your Heba Journey.</p>
            <strong>Your credits will be active until 24th December 2022 and will be live in your account as soon as you complete your profile account setup.</strong>

            <terms-and-conditions :isLondon="false" />
        </template>
    </SubscriptionLandingPage>
</template>

<script>
import SubscriptionLandingPage from './components/SubscriptionLandingPage.vue'
import TermsAndConditions from './components/TermsAndConditions.vue'

export default {
    components: { SubscriptionLandingPage, TermsAndConditions },
}
</script>
