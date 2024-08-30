<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content">
            <div class="onboard__col__wrapper">
                <h1 class="onboard__title">Now, let's learn more about you.</h1>
                 <h3 class="onboard__sub_title">What are your key focuses.</h3>

                <div class="onboard__form">
                    <button-options
                        v-model="chosenFocuses"
                        :options="focusOptions"
                        multiple
                        :columns="2"
                        align="center" />

                        <p class="onboard__description" style="margin-top: 1rem;">Select those most important to you<br>
                        <small>If you have any specialist requirements - you will be able to detail these in your PARQ before your first session.</small></p>
                        <div class="onboard__divider"></div>

                        <h3 class="onboard__sub_title">How would you rank your fitness level on a scale of 1 – 5?</h3>

                        <div class="onboard__form">
                            <form-slider-input v-model="fitnessLevel" :min="1" :max="5" />


                        </div>
                        <div class="onboard__divider"></div>
                        <h3 class="onboard__sub_title">Rate your pilates experience level on a scale of 1 – 5</h3>

                        <div class="onboard__form">
                            <form-slider-input v-model="pilatesExperience" :min="1" :max="5" />
                        </div>


                    <button class="onboard__form__submit button button--full" :disabled="formInvalid || loading" @click="submitFocuses">
                        <template v-if="formInvalid">Select at least 1 Focus</template>
                        <i v-else-if="loading" class="fas fa-spinner fa-spin"></i>
                        <template v-else>Continue</template>
                    </button>
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
import FormSliderInput from '../../../components/FormSliderInput.vue';

export default {
    components: { ButtonOptions,FormSliderInput },
    data () {
        return {
            focuses: [],
            chosenFocuses: [],
            loading: false,
            pilatesExperience: 3,
            fitnessLevel: 3,
        }
    },
    computed: {
        focusOptions () {
            return this.focuses.map(focus => {
                return { label: focus.name, value: focus.id }
            })
        },
        formInvalid () {
            return this.chosenFocuses.length < 1;
        }
    },
    methods: {
        async getFocuses () {
            try {
                const response = await axios.get('/api/onboarding/focuses');
                this.focuses = response.data;
            } catch (err) {
                console.error('Error GET focuses', err);
            }
        },
        async submitFocuses () {
            if (this.formInvalid) return;

            this.loading = true;

            try {
                await axios.post('/api/onboarding/focuses',
                    { focuses: this.chosenFocuses });
                this.submitPilatesExperience();
                this.submitFitnessLevel();

                this.$router.push('/onboard/parq');
            } catch (err) {
                console.error('Error POST focuses', err);
            } finally {
                this.loading = false;
            }
        },
        async submitPilatesExperience () {
            this.loading = true;

            try {
                await axios.post('/api/onboarding/pilates-experience',
                    { pilates_experience: this.pilatesExperience });

            } catch (err) {
                console.error('Error POST pilates experience', err);
            } finally {
                this.loading = false;
            }
        },
        async submitFitnessLevel () {
            this.loading = true;

            try {
                await axios.post('/api/onboarding/fitness-level',
                    { fitness_level: this.fitnessLevel });

            } catch (err) {
                console.error('Error POST fitness level', err);
            } finally {
                this.loading = false;
            }
        }
    },
    async mounted () {
        await this.getFocuses();
    }
}
</script>
