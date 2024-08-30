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
                        <p class="booking-confirmed__step__content">{{ step.description }}</p>
                        <template v-if="step.number == 2">
                            <router-link class="button button--full" to="/membership">Membership and Credit Packs</router-link>
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
                    title: "Credit Pack Activated",
                    description: "Your Heba Pilates credit pack has been added to your account. You can view all of your credit packs at anytime within your Memberships and Credit Packs page. "
                },

                {
                    number: 1,
                    title: "Use Your Credits",
                    description: "Your credit pack will now allow you to purchase additional in-studio slots, or at-home live class tickets, depending on the Credit Pack you have purchased. You are granted a limited amount of credits for these activites."
                },

                {
                    number: 2,
                    title: "Manage & View Credits",
                    description: "You can keep track of your credits within your profile settings."
                }
            ]
        }
    },

    mounted() {
        if (this.$route.params.status === 'success') {
            axios.post('/api/account/credit-packs/' + this.$route.params.creditPackID + '/purchase', {
                stripeId: this.$route.query.session_id
            }).then(response => {
                if (this.$route.query.ref && this.$route.query.ref != '') {
                    window.location = this.$route.query.ref;
                } else {
                    window.location = '/credit-packs/' + this.$route.params.creditPackID + '/purchase/complete';
                }
            }).catch(error => {
                console.log('ERROR');
            });
        }

        if (this.$route.params.status === 'cancel')
            this.$router.replace('/membership')
    }
}
</script>
