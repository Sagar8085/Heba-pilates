<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content">
            <div class="onboard__col__wrapper">
                <p class="onboard__subtitle onboard__subtitle--primary">Informed Consent</p>
                <h1 class="onboard__title">Physical Activity Readiness Questionnaire (PARQ)</h1>

                <form class="onboard__form" @submit.prevent="submitPARQ">
                    <checkbox-question
                        v-model="current_injuries"
                        label="Do you have any current injuries or medical conditions that could have an effect on your exercise participation in any way?"
                        name="current_injuries"
                        :details.sync="current_injuries_details" />

                    <checkbox-question
                        v-model="taking_medication"
                        label="Are you currently taking any medication or drugs that could have an effect on your exercise participation?"
                        name="taking_medication"
                        :details.sync="taking_medication_details" />

                    <checkbox-question
                        v-model="advised_by_doctor"
                        label="Have you ever been advised that you should only do physical activity recommended by a doctor? (Reasons may include having a diagnosed heart condition or chronic bone/joint problems)."
                        name="advised_by_doctor"
                        :details.sync="advised_by_doctor_details" />

                    <checkbox-question
                        v-model="currently_pregnant"
                        label="Are you currently pregnant or have you been pregnant in the last 3 months?"
                        name="currently_pregnant"
                        :details.sync="currently_pregnant_details" />

                    <h2 class="onboard__title onboard__title--secondary">Emergency Contact</h2>

                    <div class="row">
                        <div class="six columns">
                            <form-input
                                v-model="emergencyContact.first_name"
                                label="First Name"
                                required
                            />
                        </div>

                        <div class="six columns">
                            <form-input
                                v-model="emergencyContact.last_name"
                                label="Last Name"
                                required
                            />
                        </div>
                    </div>

                    <form-input
                        v-model="emergencyContact.phone_number"
                        label="Contact Number"
                        type="number"
                        allowStartingZero
                        required />

                    <form-input
                        v-model="emergencyContact.email"
                        label="Email Address"
                        type="email"
                        required />

                    <h2 class="onboard__title onboard__title--secondary">Health Commitment Statements</h2>

                    <form-input
                        v-model="termsAgreed"
                        class="form-input--checkbox-multiline"
                        type="checkbox">
                        <p slot="label">
                            I confirm that I have read, understood and agree to abide by the <span class="button button--plain button--link" @click.prevent.stop="openTermsPopup(false)">Heba Terms and Conditions</span>, <span class="button button--plain button--link" @click.prevent.stop="openTermsPopup(true)">Informed Consent</span> and <span class="button button--plain button--link" @click.prevent.stop="openTermsPopup(true)">Health Commitment Statements</span>.
                        </p>
                    </form-input>

                    <footer class="onboard__form__footer">
                        <button class="onboard__form__submit button button--full" :disabled="formInvalid || loading">
                            <template v-if="formInvalid">Please complete form</template>
                            <i v-else-if="loading" class="fas fa-spinner fa-spin"></i>
                            <template v-else>Continue</template>
                        </button>
                    </footer>
                </form>
            </div>
        </section>
        <section class="onboard__col onboard__col--image">
            <img src="/images/onboarding/onboarding--parq.jpg" alt="Heba Pilates" draggable="false" />
        </section>

        <TermsPopup v-model="showTermsPopup" ref="terms" />
    </div>
</template>

<script>
import FormInput from '../../../components/FormInput.vue';
import TermsPopup from '../../legal/TermsPopup.vue';
import CheckboxQuestion from './PARQCheckbox.vue';

export default {
    components: { CheckboxQuestion, FormInput, TermsPopup },
    data () {
        return {
            current_injuries: '',
            current_injuries_details: '',
            taking_medication: '',
            taking_medication_details: '',
            advised_by_doctor: '',
            advised_by_doctor_details: '',
            currently_pregnant: '',
            currently_pregnant_details: '',
            emergencyContact: {
                first_name: '',
                last_name: '',
                phone_number: '',
                email: ''
            },
            termsAgreed: false,
            loading: false,
            showTermsPopup: false
        }
    },
    computed: {
        formInvalid () {
            return !this.current_injuries || !this.taking_medication || !this.advised_by_doctor || !this.currently_pregnant
                || this.emergencyContact.first_name.length == 0
                || this.emergencyContact.last_name.length == 0
                || this.emergencyContact.phone_number.length == 0
                || this.emergencyContact.email.length == 0
                || !this.termsAgreed;
        },
        formData () {
            return {
                first_name: this.emergencyContact.first_name,
                last_name: this.emergencyContact.last_name,
                phone_number: this.emergencyContact.phone_number,
                email: this.emergencyContact.email,
                current_injuries: this.current_injuries,
                current_injuries_details: this.current_injuries == 'yes' ? this.current_injuries_details : '',
                taking_medication: this.taking_medication,
                taking_medication_details: this.taking_medication == 'yes' ? this.taking_medication_details : '',
                advised_by_doctor: this.advised_by_doctor,
                advised_by_doctor_details: this.advised_by_doctor == 'yes' ? this.advised_by_doctor_details : '',
                currently_pregnant: this.currently_pregnant,
                currently_pregnant_details: this.currently_pregnant == 'yes' ? this.currently_pregnant_details : ''
            }
        }
    },
    methods: {
        openTermsPopup (goToHealthStatement = false) {
            this.showTermsPopup = true;

            this.$nextTick(() => {
                if (goToHealthStatement)
                    this.$refs.terms.goToHealthCommitmentStatement()
            })
        },
        async submitPARQ () {
            if (this.formInvalid) return;

            this.loading = true;

            try {
                await axios.post('/api/onboarding/parq', this.formData);

                this.$router.push('/');
            } catch (err) {
                console.error('Error POST PARQ data', err);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
