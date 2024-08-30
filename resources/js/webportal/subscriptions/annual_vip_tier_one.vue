<template>
    <SubscriptionLandingPage :isLondon="isLondonStudio" title="Annual Unlimited VIP £1,800 per annum" type="subscription" id="19">
        <template slot="info">
            <ul>
                <li>Unlimited use of all Heba studios</li>
                <li>Preferential booking privilege <small>(anytime)</small></li>
                <li>1 complimentary Guest pass every month</li>
                <li>Yearly subscription</li>
            </ul>
            <p>Once payment is confirmed you will be able to complete your unique Heba profile account, book your first session and begin your Heba Journey.</p>
            <strong>Your unlimited credits will be active from date of purchase and will be live in your account as soon as you complete your profile account setup. You will be able to book your studio reservations directly via the Heba app as far in advance as suits your personal schedule.</strong>

            <terms-and-conditions :isLondon="isLondonStudio" noteText="Your annual recurring subscription plan will renew each year on the same date as original purchase until you confirm cancellation." />
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
