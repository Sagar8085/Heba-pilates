<template>
    <div class="onboard__cols">
        <section class="onboard__col onboard__col--content">
            <div class="onboard__col__wrapper">
                <h1 class="onboard__title">Let's get moving</h1>

                <form class="onboard__form" @submit.prevent="submitDetails">
                    <div class="row">
                        <div class="six columns">
                            <form-input v-model="details.first_name" placeholder="John" label="First Name" :error="getError('first_name')" required />
                        </div>
                        <div class="six columns">
                            <form-input v-model="details.last_name" placeholder="Appleseed" label="Last Name" :error="getError('last_name')" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="twelve columns">
                            <form-input v-model="details.email" placeholder="john.appleseed@example.com" label="Email Address" :error="getError('email')" type="email" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="twelve columns">
                            <form-input v-model="details.password" placeholder="********" label="Create Password" :type="passwordType" :error="getError('password')" autocomplete="new-password" required>
                                <template v-slot:after-input>
                                    <button class="auth__input-button" type="button" @click="togglePassword" tabindex="-1">
                                        <i class="material-icons">{{ passwordType == 'password' ? 'visibility' : 'visibility_off' }}</i>
                                    </button>
                                </template>
                            </form-input>
                        </div>
                    </div>

                    <div class="row">
                        <div class="twelve columns">
                            <form-input v-model="details.phone_number" placeholder="Enter Phone Number" label="Phone Number" :error="getError('phone_number')" type="text" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="twelve columns">
                            <date-of-birth-input
                                label="Date of Birth"
                                v-model="details.dob"
                                :day.sync="details.dob_day"
                                :month.sync="details.dob_month"
                                :year.sync="details.dob_year"
                                :dayError="getError('dob_day')"
                                :monthError="getError('dob_month')"
                                :yearError="getError('dob_year')"
                             />
                         </div>
                     </div>

                    <div class="row">
                        <div class="six columns">
                            <form-input
                                v-model="details.gender"
                                label="Gender"
                                type="select"
                                staticLabel
                                placeholder="Select one"
                                :error="getError('gender')">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Prefer not to say</option>
                            </form-input>
                        </div>

                        <div class="six columns">
                            <form-input :error="getError('home_studio_id')" label="Select Hub Studio" v-model="details.home_studio_id" type="select" placeholder="Studio" required>
                                <option :value="studio.id" v-for="studio in studios">{{ studio.name }}</option>
                            </form-input>
                        </div>
                    </div>

                     <form-input label="How did you hear about us?" v-model="details.marketing_hear_about_us" type="select" placeholder="-- Select an option">
                         <option value="referral">Referral</option>
                         <option value="google">Google</option>
                         <option value="social-media">Social media</option>
                         <option value="website">Website</option>
                         <option value="leaflet-flyer">Leaflet / Flyer</option>
                         <option value="local-event">Local Event</option>
                         <option value="local-signs">Local Signs</option>
                         <option value="radio">Radio</option>
                         <option value="tv">TV</option>
                         <option value="returning-guest">Returning Guest</option>
                     </form-input>


                    <button class="onboard__form__submit button button--full" :disabled="formInvalid || loading">
                        <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                        <template v-else>Create Account</template>
                    </button>
                </form>
            </div>
        </section>
        <section class="onboard__col onboard__col--image">
            <img src="/images/onboarding/onboarding--account.jpg" alt="Heba Pilates" draggable="false" />
        </section>
    </div>
</template>

<script>
import FormInput from '../../../components/FormInput.vue';
import DateOfBirthInput from '../DateOfBirthInput.vue';

export default {
    components: { FormInput, DateOfBirthInput },
    data () {
        return {
            loading: false,
            passwordType: 'password',
            details: {
                first_name: '',
                last_name: '',
                email: '',
                password: '',
                gender: '',
                phone_number: '',
                home_studio_id: '',
                dob: '',
                dob_day: '',
                dob_month: '',
                dob_year: '',
                heard_about_us: '',
                marketing_hear_about_us: ''
            },
            errors: {},
            studios: {}
        }
    },
    computed: {
        formInvalid () {
            return this.details.first_name.length == 0
            || this.details.last_name.length == 0
            || this.details.email.length == 0
            || this.details.password.length == 0;
        }
    },
    mounted(){
        this.getStudios();

        console.log(this.studios.data);

    },
    methods: {
        togglePassword () {
            this.passwordType = this.passwordType === 'password'
                ? 'text' : 'password';
        },
        getError (key) {
            return this.errors && this.errors[key] ? this.errors[key][0] : null
        },
        getStudios(){

                axios.get('/api/gyms').then(response => {
                    this.studios = response.data;
                })
        },
        async submitDetails () {
            if (this.formInvalid) return;

            this.loading = true;

            try {
                const response = await axios.post('/api/register', this.details);
                const token = response.data.user.api_token;

                localStorage.setItem('fc-usertoken', token);
                sessionStorage.setItem('fc-usertoken', token);
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

                window.location.href = '/onboard/intro-pack';
            } catch (err) {
                console.error('Registration Error:', err);
                this.errors = err.response.data.errors;
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
