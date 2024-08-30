<template>
    <SubscriptionLandingPage :isLondon="isLondonStudio" title="Single session <br/>£24" type="payment" id="2">
        <template slot="info">
            <ul>
                <li>2 week validity</li>
                <li>Book 1 week in advance</li>
            </ul>
            <p>Once payment is confirmed you will be able to complete your unique Heba profile account, book your first session and begin your Heba Journey.</p>
            <strong>Your 1 credit will be active from date of purchase and will be live in your account as soon as you complete your profile account setup. Your 1 credit will expire 2 weeks from date of purchase. You will be able to book one week in advance for your studio reservation directly via the Heba app. </strong>

            <terms-and-conditions :isLondon="isLondonStudio" noteText="Your 1 credit is valid for 2 weeks from date of purchase and allows you to book a session one week in advance. If you cancel a session less than 1 hour before reserved start time your credit will be redeemed and will not be refundable." />
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
