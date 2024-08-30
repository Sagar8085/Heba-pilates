<template>
    <div class="wrapper">
        <div class="onboarding__main">
            <img src="/images/logos/heba-logo.png" class="onboarding__title--login--image" >


            <div v-if="!emailSuccess && !confirmationFailure">
                <h1 class="onboarding__title--login">Confirm Account</h1>
                <h2 class="onboarding__subtitle onboarding__subtitle--error" v-if="this.error">Error: Please contact your Fitness Concierge administrator</h2>
            </div>

            <div class="auth__form" v-if="emailSuccess">
                <h1 class="onboarding__title--login">User Confirmed</h1>
                    <h2 class="onboarding__subtitle onboarding__subtitle">Great, your account has been confirmed. Welcome to Heba Pilates!</h2>

                <div class="auth__group">
                    <router-link class="button button--full" to="/login">Login Now</router-link>
                </div>
            </div>

            <div class="auth__form" v-if="confirmationFailure">
                <h1 class="onboarding__title--login">Confirmation Failed</h1>
                <div class="auth__group" v-if="!loading">
                    <label class="auth__label auth__label--light">Sorry, it looks like this confirmation request has not been completed.</label>
                </div>
                <i v-else class="fas fa-spinner fa-spin"></i>

                <div class="auth__group">
                    <router-link class="button button--full" to="/login" v-if="!loading">Back to login</router-link>
                    <button type="button" class="button button--full" disabled v-else>
                        <i class="fas fa-spinner fa-spin"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import FormInput from '../../components/FormInput.vue';

    export default {
        components: { FormInput },
        data () {
            return {
                email: '',
                error: false,
                loading: false,
                emailSuccess: false,
                confirmationFailure: false,
                modalMessage: '',
                password: '',
                password_confirmation: ''
            };
        },

        mounted() {
            this.confirm();
        },

        methods: {
            confirm() {
                axios.post('/api/confirm', {
                    token: this.$route.params.token,
                }).then(response => {
                    this.confirmationFailure = false;
                    this.emailSuccess = true;
                    console.log(response);
                }).catch(error => {
                    console.log(error)
                    this.confirmationFailure = true;
                    this.error = error.message;
                });
            }
        }
    };
</script>
