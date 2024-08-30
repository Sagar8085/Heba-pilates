<template>
    <div v-else class="wrapper">
        <div :class="{ onboarding__main: true, 'onboarding__main--wide': step2 }">
            <img src="/images/logos/heba-logo.png" class="onboarding__title--login--image" >
            <form class="auth__form" >
                <p v-if="errors && errors.length">
                    <b>Please correct the following error(s):</b>
                    <ul>
                        <li v-for="error in errors" v-bind:key="error.id">{{ error }}</li>
                    </ul>
                </p>

                <div>
                    <h1 class="onboarding__title--login">Sign Up</h1>

                    <form @submit.prevent="signUp">
                        <div class="auth__group">

                            <div class="row">
                                <div class="six columns" style="margin-bottom: .5rem">
                                    <form-input v-model="fName" placeholder="John" label="First Name" :error="getError('first_name')" required />
                                </div>

                                <div class="six columns" style="margin-bottom: .5rem">
                                    <form-input v-model="lName" placeholder="Appleseed" label="Last Name" :error="getError('last_name')" required />
                                </div>
                            </div>

                            <form-input v-model="email" placeholder="john.appleseed@example.com" label="Email Address" :error="getError('email')" type="email" required />

                            <date-of-birth-input
                                label="Date of Birth"
                                v-model="dob"
                                :day.sync="dob_day"
                                :month.sync="dob_month"
                                :year.sync="dob_year"
                                :error="getError('dob')" />

                            <form-input
                                v-model="genderPicked"
                                label="Gender"
                                type="select"
                                staticLabel
                                placeholder="Select one"
                                :error="getError('gender')">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Prefer not to say</option>
                            </form-input>

                            <form-input v-model="password" placeholder="********" label="Create Password" :type="type" :error="getError('password')" autocomplete="new-password" required>
                                <template v-slot:after-input>
                                    <button class="auth__input-button" type="button" @click="showPassword" tabindex="-1">
                                        <i class="material-icons">{{ type == 'password' ? 'visibility' : 'visibility_off' }}</i>
                                    </button>
                                </template>
                            </form-input>
                            <form-input v-model="password_confirmation" placeholder="********"  :type="type" label="Repeat Password" autocomplete="new-password" required>
                                <template v-slot:after-input>
                                    <button class="auth__input-button" type="button" @click="showPassword" tabindex="-1">
                                        <i class="material-icons">{{ type == 'password' ? 'visibility' : 'visibility_off' }}</i>
                                    </button>
                                </template>
                            </form-input>
                            <password :strength-meter-only="true" v-model="password"/>

                            <p class="password-text" style="margin-bottom: 1rem; border-bottom: 1px solid #999; padding-bottom: 1rem;">For a strong password use 8 or more characters and a mix of letters, numbers and symbols</p>

                            <form-input type="checkbox" v-model="agreeTerms" label="You confirm you have read and agree to our Terms and Conditions" :error="getError('agreeTerms')" required />
                            <form-input type="checkbox" v-model="agreePrivacy" label="You confirm you have read and agree to our Privacy Policy" :error="getError('agreePrivacy')" required />
                        </div>

                        <div class="auth__group">
                            <button class="button button--full" type="submit" v-if="!loading" :disabled="signUpDisabled" @click="signUp()">Join Heba Pilates</button>
                            <button type="button" class="button button--full" v-else disabled>
                                <i class="fas fa-spinner fa-spin"></i>
                            </button>
                        </div>
                    </form>

                    <div class="auth__group--forgot">
                        <div class="auth__group--forgot">
                            <router-link to="/login"><span class="auth__group--forgot--signup">Already have an account?</span> Log in</router-link>
                        </div>
                        <div class="auth__group--forgot auth__group--forgot--small">
                            <span class="auth__group--forgot--signup">By signing up you agree to our</span> <a href="/terms" target="_blank">Terms and Conditions</a> <span class="auth__group--forgot--signup">and </span> <a href="/privacy" target="_blank">Privacy Policy</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Password from 'vue-password-strength-meter';
import FormInput from '../../components/FormInput.vue';
import DateOfBirthInput from './DateOfBirthInput.vue';
import RegisterSubscription from './RegisterSubscription.vue';
import ButtonOptions from '../../components/ButtonOptions.vue';
import RegisterSubscriptionCard from './RegisterSubscriptionCard.vue';

    export default {
        components: { Password, FormInput, DateOfBirthInput, ButtonOptions, RegisterSubscription, RegisterSubscriptionCard },
        //TODO remove token
        data () {
            return {
                contentCategories: [],
                errors: {},
                gyms: [],
                selectedGym: '',
                subscription: null,
                step1: true,
                step2: false,
                step3: false,
                step4: false,
                step: '',
                fName: '',
                lName: '',
                email: '',
                tel: '',
                password: '',
                password_confirmation: '',
                dob: '',
                dob_day: NaN,
                dob_month: '',
                dob_year: NaN,
                genderPicked: '',
                error: false,
                loading: false,
                type: 'password',
                token: '',
                bearer: '',
                user_id: null,
                agreeTerms: false,
                agreePrivacy: false,
                address: {
                    first_line: '',
                    second_line: '',
                    town: '',
                    county: '',
                    postcode: ''
                },
                categories: [],
                categoryOptions: [
                    { label: 'Classical Pilates', value: 'category-1' },
                    { label: 'Mat Pilates', value: 'category-2' },
                    { label: 'Reformer Pilates', value: 'category-3' },
                    { label: 'Clinical Pilates', value: 'category-4' },
                    { label: 'Contemporary Pilates', value: 'category-5' },
                    { label: 'Other', value: 'category-6' }
                ]
            };
        },

        mounted() {
            this.loadContentPreferences();
        },

        watch: {
            categories(value) {
                console.log(value);
                axios.patch('/api/content-preferences', {
                    preference: value,
                    bearer: this.bearer
                }).then(response => {
                    console.log('saved preference')
                })
            }
        },

        updated() {
            this.changeStep();
        },

        computed: {
            signUpDisabled () {
                if (this.step1) {
                    return this.fName.length == 0 ||
                        this.lName.length == 0 ||
                        this.email.length == 0 ||
                        (isNaN(this.dob_day) || this.dob_day < 1 || this.dob_day > 31) ||
                        (this.dob_month.length == 0 || isNaN(this.dob_month)) ||
                        (isNaN(this.dob_year) || this.dob_year.toString().length !== 4) ||
                        this.genderPicked.length == 0 ||
                        this.password.length == 0 ||
                        this.password_confirmation.length == 0 ||
                        this.password !== this.password_confirmation ||
                        !this.agreeTerms ||
                        !this.agreePrivacy;
                } else if (this.step3) {
                    return this.address.first_line.length == 0 ||
                        this.address.second_line.length == 0 ||
                        this.address.town.length == 0 ||
                        this.address.county.length == 0 ||
                        this.address.postcode.length == 0;
                } else false
            }
        },

        methods: {
            setSubscription (subscription) {
                this.subscription = subscription;
                const app = document.getElementById('app');
                app.scrollIntoView();
                subscription == null
                    ? app.classList.remove('sign-up--form')
                    : app.classList.add('sign-up--form');
            },

            getError (key) {
                return this.errors && this.errors[key] ? this.errors[key][0] : null
            },

            showPassword() {
                if (this.type === 'password') {
                    this.type = 'text'
                } else {
                    this.type = 'password'
                }
            },

            loadContentPreferences() {
                axios.get('/api/content-preferences').then(response => {
                    this.contentCategories = response.data;
                });
            },

            dateSplit() {
                const fulldate = new Date(this.dob);
                const date = fulldate.getDate();
                const month = fulldate.getMonth();
                const year = fulldate.getFullYear();

                this.dob_day = date;
                this.dob_month = month + 1;
                this.dob_year = year;
            },

            changeStep() {
                if (this.step1 == true) {
                    // this.dateSplit();
                    this.step = '/step-1';
                } else if (this.step2 == true) {
                    this.step = '/step-2';
                } else if (this.step3 == true) {
                    this.step = '/step-3';
                } else if (this.step4 == true) {
                    this.step = '/step-4';
                } else {
                    this.step = '';
                }
            },

            scrollToTop() {
                document.getElementById('app').scrollIntoView();
            },

            signUp() {
                if (this.signUpDisabled) return;
                this.loading = true;

                axios.post('/api/register' + this.step, {
                    first_name: this.fName,
                    last_name: this.lName,
                    email: this.email,
                    phone_number: this.tel,
                    gym_id: this.selectedGym,
                    gender: this.genderPicked,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    dob: this.dob,
                    dob_day: this.dob_day,
                    dob_month: this.dob_month,
                    dob_year: this.dob_year,
                    token: this.token,
                    user_id: this.user_id,
                    subscription: this.subscription,
                    categories: this.categories
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        localStorage.setItem('fc-usertoken', response.data.user.api_token);
                        sessionStorage.setItem('fc-usertoken', response.data.user.api_token)
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.user.api_token
                        // window.location.href = '/onboard';
                        window.location.href = '/';
                    })
                    .catch(error => {
                        console.log(error);
                        this.errors = error.response.data.errors;
                    })
                    .finally(() => {
                        this.scrollToTop();
                        this.loading = false
                    });
            }
        }
    };
</script>
