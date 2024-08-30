<template>
    <div>
        <PageHeader title="Please Wait" subtitle="Membership is being processed, please don't leave or refresh this page" icon="event" />
    </div>
</template>

<script>
import axios from 'axios';
import PageHeader from './../layout/PageHeader.vue';

export default {
    components: {
       PageHeader
   },

    data() {
        return {
            steps: [
                {
                    number: 0,
                    title: "Membership Activated",
                    description: "Your Heba Pilates plan has been added to your account, your membership is billed monthly and you can cancel this at any time within your account settings. Your Direct Debit can take 3 business days to process, so please make sure you have funds available to be deducted or your services might be restricted."
                },

                {
                    number: 1,
                    title: "Browse Fitness Content",
                    description: "Whether you prefer on-demand or live classes, without equipment or with a pilates machine, you'll be sure to find something for you."
                },

                {
                    number: 2,
                    title: "Watch Anytime, Anywhere!",
                    description: "Watch content on your laptop / computer or you can download our mobile app for iOS and Android devices so you can keep fit on the move."
                }
            ]
        }
    },

    mounted() {
        axios.post('/api/admin/members/' + this.$route.params.id + '/memberships/callback', {
            stripeSessionID: this.$route.query.session_id,
            tier: this.$route.query.tier
        }).then(response => {
            console.log(response.data);
            window.location = '/admin/members/' + this.$route.params.id + '/?membership_purchase=complete';
        }).catch(error => {
            console.log('ERROR');
        });
        console.log('success!')
    }
}
</script>
