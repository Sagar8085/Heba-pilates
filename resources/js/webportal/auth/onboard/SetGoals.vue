<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content">
            <div class="onboard__col__wrapper">
                <h1 class="onboard__title">What would you like to achieve?</h1>

                <p class="onboard__subtitle">Select One</p>

                <div class="onboard__form">
                    <button-options v-model="chosenGoal" :options="goalOptions" @input="submitGoal" />
                </div>
            </div>
        </section>
        <section class="onboard__col onboard__col--image">
            <img src="/images/onboarding/onboarding--goals.png" alt="Heba Pilates" draggable="false" />
        </section>
    </div>
</template>

<script>
import ButtonOptions from '../../../components/ButtonOptions.vue';

export default {
    components: { ButtonOptions },
    data () {
        return {
            goals: [],
            chosenGoal: null,
            loading: true
        }
    },
    computed: {
        goalOptions () {
            return this.goals.map(goal => {
                return { label: goal.name, value: goal.id }
            })
        }
    },
    methods: {
        async getGoals () {
            try {
                const response = await axios.get('/api/onboarding/goals');
                this.goals = response.data;
            } catch (err) {
                console.error('Error GET goals', err);
            }
        },
        async submitGoal () {
            if (this.chosenGoal == null) return;

            try {
                await axios.post('/api/onboarding/goals', 
                    { goals: [ this.chosenGoal ] });

                this.$router.push('/onboard/focuses');
            } catch (err) {
                console.error('Error POST goals', err);
            }
        }
    },
    async mounted () {
        await this.getGoals();
    }
}
</script>