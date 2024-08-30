<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content">
            <div class="onboard__col__wrapper">
                <h1 class="onboard__title">How would you rank your fitness level on a scale of 1 – 5?</h1>

                <div class="onboard__form">
                    <form-slider-input v-model="fitnessLevel" :min="1" :max="5" />

                    <button class="onboard__form__submit button button--full" :disabled="loading" @click="submitFitnessLevel">
                        <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                        <template v-else>Continue</template>
                    </button>
                </div>
            </div>
        </section>
        <section class="onboard__col onboard__col--image">
            <img src="/images/onboarding/onboarding--fitness.png" alt="Heba Pilates" draggable="false" />
        </section>
    </div>
</template>

<script>
import FormSliderInput from '../../../components/FormSliderInput.vue';

export default {
    components: { FormSliderInput },
    data () {
        return {
            fitnessLevel: 3,
            loading: false
        }
    },
    methods: {
        async submitFitnessLevel () {
            this.loading = true;

            try {
                await axios.post('/api/onboarding/fitness-level', 
                    { fitness_level: this.fitnessLevel });

                this.$router.push('/onboard/pilates-experience');
            } catch (err) {
                console.error('Error POST fitness level', err);
            } finally { 
                this.loading = false; 
            }
        }
    }
}
</script>