<template>
<div class="wrapper subscriptions subscriptions--cancel">
    <h1 class="subscriptions__title">Cancel your subscription?</h1>
    <h2 class="subscriptions__subtitle">Click “Confirm Cancellation” below to cancel your subscription.</h2>
    <p class="subscriptions__text">Cancellation will be effective at the end of your current billing period on <strong>{{ formatDate('01/01/2022')}}</strong>.</p>
    <div class="subscriptions__buttons">
        <router-link to="/membership" class="button button--outline">Go Back</router-link>
        <button class="button">Confirm Cancellation</button>
    </div>
</div>
</template>

<script>
import moment from 'moment'

export default {
    methods: {
        formatDate (dateString) {
            return moment(dateString).format('Do MMMM YYYY');
        }
    }
}
</script>