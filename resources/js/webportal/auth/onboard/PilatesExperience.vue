<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content">
            <div class="onboard__col__wrapper">
                <h1 class="onboard__title">Rate your pilates experience level on a scale of 1 – 5</h1>

                <div class="onboard__form">
                    <form-slider-input v-model="pilatesExperience" :min="1" :max="5" />

                    <button class="onboard__form__submit button button--full" :disabled="loading" @click="submitPilatesExperience">
                        <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                        <template v-else>Continue</template>
                    </button>
                </div>
            </div>
        </section>
        <section class="onboard__col onboard__col--image">
            <img src="/images/onboarding/onboarding--pilates.png" alt="Heba Pilates" draggable="false" />
        </section>
    </div>
</template>

<script>
import FormSliderInput from '../../../components/FormSliderInput.vue';

export default {
    components: { FormSliderInput },
    data () {
        return {
            pilatesExperience: 3,
            loading: false
        }
    },
    methods: {
        async submitPilatesExperience () {
            this.loading = true;

            try {
                await axios.post('/api/onboarding/pilates-experience', 
                    { pilates_experience: this.pilatesExperience });

                this.$router.push('/onboard/parq');
            } catch (err) {
                console.error('Error POST pilates experience', err);
            } finally { 
                this.loading = false; 
            }
        }
    }
}
</script>