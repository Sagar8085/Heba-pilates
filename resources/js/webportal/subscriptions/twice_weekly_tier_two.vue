<template>
    <SubscriptionLandingPage :isLondon="isLondonStudio" title="Twice Weekly Plan £110 per month" type="subscription" id="14">
        <template slot="info">
            <ul>
                <li>8 session credits per month</li>
                <li>Preferential booking privilege <small>(1 month ahead)</small></li>
                <li>Special rate Top Up sessions <small>(only £14 each)</small></li>
                <li>Monthly recurring subscription</li>
            </ul>
            <p>Once payment is confirmed you will be able to complete your unique Heba profile account, book your first session and begin your Heba Journey.</p>
            <strong>Your first eight credits will be active from date of purchase and will be live in your account as soon as you complete your profile account setup. You will be able to book one month in advance for all studio reservations directly via the Heba app.</strong>

            <terms-and-conditions :isLondon="isLondonStudio" noteText="Your monthly recurring subscription plan of eight credits (valid for one month each) will renew each month on the same date as original purchase until you confirm cancellation." />
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
