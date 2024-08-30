<template>
    <div>
        <div class="booking-confirmed" v-if="this.$route.params.status === 'success'">
            <header class="booking-confirmed__header">
                <h1 class="wrapper">Please wait...</h1>
            </header>
        </div>

        <div class="booking-confirmed" v-if="this.$route.params.status === 'complete'">
            <header class="booking-confirmed__header">
                <h1 class="wrapper">Thanks for your purchase!</h1>
            </header>
            <div class="wrapper">
                <h2>What now?</h2>
                <div class="booking-confirmed__steps">
                    <section v-for="step in steps" :key="step.number" class="booking-confirmed__step">
                        <div class="booking-confirmed__step__header">

                            <span class="booking-confirmed__step__header__number" v-if="step.number > 0">{{ step.number }}</span>
                            <span class="booking-confirmed__step__header__number" v-else><i class="far fa-check-circle"></i></span>
                            <h3>{{ step.title }}</h3>
                        </div>
                        <p class="booking-confirmed__step__content">
                            {{ step.description }}
                        </p>
                        <template v-if="step.number == 2">
                            <router-link class="button button--full" to="/membership">View Membership</router-link>
                        </template>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
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
        if (this.$route.params.status === 'success') {
            axios.post('/api/account/membership/' + this.$route.params.tier + '/purchase', {
                stripeId: this.$route.query.session_id
            }).then(response => {
                console.log(response.data);
                window.location = '/membership/' + this.$route.params.tier + '/bacs/complete';
            }).catch(error => {
                console.log('ERROR');
            });
        }
    }
}
</script>
