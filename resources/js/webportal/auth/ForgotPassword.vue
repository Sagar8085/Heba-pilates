<template>
    <div class="wrapper">
        <div class="onboarding__main">
            <img src="/images/logos/heba-logo.png" class="onboarding__title--login--image" >


            <form class="auth__form" v-if="!emailSuccess" @submit.prevent="reset('send-email')">
                <h1 class="onboarding__title--login">Forgot Password</h1>
                <h2 class="onboarding__subtitle onboarding__subtitle--error" v-if="this.error">Sorry, looks like we couldn't find an account with this email address.</h2>
                <div class="auth__group" v-if="!loading">
                    <form-input v-model="email" label="Email Address" type="email" />
                </div>
                <i v-else class="fas fa-spinner fa-spin"></i>

                <div class="auth__group">
                    <button class="button button--full" :disabled="email.length == 0" @click.prevent="reset('send email')" v-if="!loading">Reset password</button>
                    <button type="button" class="button button--full" disabled v-else>
                        <i class="fas fa-spinner fa-spin"></i>
                    </button>
                </div>
            </form>

            <div class="auth__form" v-if="emailSuccess">
                <h1 class="onboarding__title--login">Email Sent</h1>
                <div class="auth__group" v-if="!loading">
                    <label class="auth__label auth__label--light">Click the link in the email to reset your password. If you can’t find it don’t forget to check your spam folder.</label>
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
                modalMessage: ''
            };
        },

        mounted() {
        },

        methods: {
            reset(x) {
                if (this.email.length == 0) return;

                if (x === 'send email') {
                    axios.post('/api/forgot-password', {
                        email: this.email
                    })
                    .then(response => {
                        if (response.data.status === 'failure') {
                            this.error = true;
                        } else {
                            this.emailSuccess = true;
                        }
                    })
                    .catch(error => {
                        console.log('ERROR');
                    })
                    .finally(() => this.loading = false);
                } else if (x === 'email sent') {
                    window.location.href = '/login'
                }

            }
        }
    };
</script>
