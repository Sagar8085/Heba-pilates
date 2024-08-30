<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content">
            <div class="onboard__col__wrapper">
                <h1 class="onboard__title">Select any joints and body parts you feel you need to focus on</h1>

                <div class="onboard__form">
                    <button-options 
                        v-model="chosenFocuses" 
                        :options="focusOptions"
                        multiple />

                    <button class="onboard__form__submit button button--full" :disabled="formInvalid || loading" @click="submitFocuses">
                        <template v-if="formInvalid">Select at least 3</template>
                        <i v-else-if="loading" class="fas fa-spinner fa-spin"></i>
                        <template v-else>Continue</template>
                    </button>
                </div>
            </div>
        </section>
        <section class="onboard__col onboard__col--image">
            <img src="/images/onboarding/onboarding--body-focuses.png" alt="Heba Pilates" draggable="false" />
        </section>
    </div>
</template>

<script>
import ButtonOptions from '../../../components/ButtonOptions.vue';

export default {
    components: { ButtonOptions },
    data () {
        return {
            focuses: [],
            chosenFocuses: [],
            loading: false
        }
    },
    computed: {
        focusOptions () {
            return this.focuses.map(focus => {
                return { label: focus.name, value: focus.id }
            })
        },
        formInvalid () {
            return this.chosenFocuses.length < 3;
        }
    },
    methods: {
        async getFocuses () {
            try {
                const response = await axios.get('/api/onboarding/body-part-focuses');
                this.focuses = response.data;
            } catch (err) {
                console.error('Error GET focuses', err);
            }
        },
        async submitFocuses () {
            if (this.formInvalid) return;

            this.loading = true;

            try {
                await axios.post('/api/onboarding/body-part-focuses', 
                    { focuses: this.chosenFocuses });

                this.$router.push('/onboard/fitness-level');
            } catch (err) {
                console.error('Error POST focuses', err);
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