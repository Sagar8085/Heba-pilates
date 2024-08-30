<template>
    <SubscriptionLandingPage :isLondon="isLondonStudio" title="10 sessions <br/>£230" type="payment" id="13">
        <template slot="info">
            <ul>
                <li>3 month validity</li>
                <li>Book 1 week in advance</li>
                <li>£23 per visit</li>
            </ul>
            <p>Once payment is confirmed you will be able to complete your unique Heba profile account, book your first session and begin your Heba Journey.</p>
            <strong>Your 10 credits will be active from date of purchase and will be live in your account as soon as you complete your profile account setup. All credits will expire 3 months from date of purchase. You will be able to book one week in advance for all studio reservations directly via the Heba app.</strong>

            <terms-and-conditions :isLondon="isLondonStudio" noteText="Your 10 credits are valid for 3 months from date of purchase and allow you to book a session one week in advance. Any cancelled sessions less than 1 hour before reserved start time will be non refundable." />
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
