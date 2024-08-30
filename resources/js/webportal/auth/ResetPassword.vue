<template>
    <div class="wrapper">
        <div class="onboarding__main">
            <img src="/images/logos/heba-logo.png" class="onboarding__title--login--image" >


            <form class="auth__form" v-if="!emailSuccess && !resetFailed" @submit.prevent="reset('send-email')">
                <h1 class="onboarding__title--login">Forgot Password</h1>
                <h2 class="onboarding__subtitle onboarding__subtitle--error" v-if="this.error">Invalid account details, please try again.</h2>
                <div class="auth__group" v-if="!loading">
                    <form-input v-model="password" label="New Password" type="password" />
                    <form-input v-model="password_confirmation" label="Confirm New Password" type="password" />
                </div>
                <i v-else class="fas fa-spinner fa-spin"></i>

                <div class="auth__group">
                    <button class="button button--full" :disabled="password.length > 0 && password === password_confirmation ? false : true" @click.prevent="reset('send email')" v-if="!loading">Reset password</button>
                    <button type="button" class="button button--full" disabled v-else>
                        <i class="fas fa-spinner fa-spin"></i>
                    </button>
                </div>
            </form>

            <div class="auth__form" v-if="emailSuccess">
                <h1 class="onboarding__title--login">Password Updated</h1>
                <div class="auth__group" v-if="!loading">
                    <label class="auth__label auth__label--light">Great, your password has been updated and you can now login to your account as normal.</label>
                </div>
                <i v-else class="fas fa-spinner fa-spin"></i>

                <div class="auth__group">
                    <router-link class="button button--full" to="/login" v-if="!loading">Back to login</router-link>
                    <button type="button" class="button button--full" disabled v-else>
                        <i class="fas fa-spinner fa-spin"></i>
                    </button>
                </div>
            </div>

            <div class="auth__form" v-if="resetFailure">
                <h1 class="onboarding__title--login">Reset Failed</h1>
                <div class="auth__group" v-if="!loading">
                    <label class="auth__label auth__label--light">Sorry, it looks like this reset request has expired or does not exist.</label>
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
                resetFailure: false,
                modalMessage: '',
                password: '',
                password_confirmation: '',
                resetFailed: false
            };
        },

        mounted() {
            console.log('on reset pass')
        },

        methods: {
            reset(x) {
                axios.post('/api/reset-password', {
                    token: this.$route.params.token,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                })
                .then(response => {
                    console.log();
                    if (response.data.status === 'failure') {
                        this.resetFailure = true;
                    } else {
                        this.emailSuccess = true;
                    }
                })
                .catch(error => {
                    console.log('ERROR');
                })
                .finally(() => this.loading = false);
            }
        }
    };
</script>
