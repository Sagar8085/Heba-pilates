<template>
    <SubscriptionLandingPage :isLondon="isLondonStudio" title="Unlimited Plan <br/> £155 per month" type="subscription" id="12">
        <template slot="info">
            <ul>
                <li>Unlimited use of all Heba studios <small>(outside London)</small></li>
                <li>Preferential booking privilege</li>
                <li>1 complimentary Guest pass per month</li>
                <li>Monthly recurring subscription</li>
            </ul>
            <p>Once payment is confirmed you will be able to complete your unique Heba profile account, book your first session and begin your Heba Journey.</p>
            <strong>Your unlimited credits will be active from date of purchase and will be live in your account as soon as you complete your profile account setup. You will be able to book one month in advance for all studio reservations directly via the Heba app.</strong>

            <terms-and-conditions :isLondon="isLondonStudio" noteText="Your monthly recurring subscription plan will renew each month on the same date as original purchase until you confirm cancellation." />
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
