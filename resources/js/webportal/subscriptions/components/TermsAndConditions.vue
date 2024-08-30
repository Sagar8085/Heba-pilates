<template>
    <div class="subscription-lp__grid">
        <div>
            <p class="subscription-lp__title subscription-lp__title--small">Terms and Conditions Apply</p>
            <strong>How it works:</strong>
            <p>
                After a successful payment, your Heba account will be created and the sessions will be assigned to it. You will also receive an email with account access instructions shortly after we receive payment. 
When you complete your account set up (1 minute) you will be able to book in your sessions at any of our Heba locations<template v-if="!isLondon"> (outside of London)</template>.
            </p>
        </div>
        <div v-if="noteText">
            <strong>Please note:</strong>
            <p v-html="noteText"></p>
        </div>

        <div>
            <router-link to="/terms">View full terms of service.</router-link>
        </div>
    </div>
</template>

<script>
export default {
    props: { 
        isLondon: {
            type: Boolean,
            default: false
        },
        noteText: {
            type: String
        }
    }
}
</script>