<template>
    <div>
        <div class="booking-confirmed">
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

                            <div class="booking-confirmed__step__content" v-if="step.number === 2" style="padding-top: 0;">
                                <label class="card-link__tag card-link__tag--alt">PREMIUM</label>
                                Be sure to lookout for the PREMIUM label. This means the content is part of your plan.
                            </div>
                        </p>
                        <template v-if="step.number == 2">
                            <router-link class="button button--full" to="/">My Dashboard</router-link>
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
                    title: "Subscriptions Activated",
                    description: "Your Fitness Concierge plan has been added to your account, your subscription is billed monthly and you can cancel this at any time within your account settings."
                },

                {
                    number: 1,
                    title: "Browse Fitness Content",
                    description: "Whether it be classes, podcasts or workouts, with unlimited access to all of our paid fitness content, you'll be sure to find something for you."
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
            axios.post('/api/account/subscription/' + this.$route.params.tier + '/purchase').then(response => {
                console.log(response.data);
            }).catch(error => {
                console.log('ERROR');
            });
        }
    }
}
</script>
