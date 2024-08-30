<template>
    <div class="wrapper">
        <div class="onboarding__main">
            <div class="onboarding__main__card">
                <img src="/images/logos/heba-logo.png" class="onboarding__title--login--image" >
                <h1 class="onboarding__title--login">Log In</h1>
                <h2 class="onboarding__subtitle onboarding__subtitle--error" v-if="this.error">Invalid account details, please try again.</h2>

                <form class="auth__form" @submit.prevent="login">
                    <div>
                        <form-input ref="email" v-model="email" type="email" label="Email Address" />
                        <form-input ref="password" v-model="password" label="Password" :type="type">
                            <template v-slot:label>
                                <div class="auth__label">Password</div>
                                <!-- <router-link class="auth__forgot" to="/forgot" tabindex="-1">Forgot password?</router-link> -->
                            </template>
                            <template v-slot:after-input>
                                <button class="auth__input-button" type="button" tabindex="-1" @click="showPassword">
                                    <i class="material-icons">{{ type == 'password' ? 'visibility' : 'visibility_off' }}</i>
                                </button>
                            </template>
                        </form-input>
                    </div>
                    <div class="auth__group auth__group--button">
                        <button
                            class="button button--full"
                            type="submit"
                            :disabled="loginDisabled"
                            @click.prevent="login">
                            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                            <template v-else>Login</template>
                        </button>
                    </div>
                    <div class="auth__group--forgot">
                        <router-link class="auth__forgot" to="/forgot" tabindex="-1">Forgot password?</router-link>
                        <router-link to="/signup"><span class="auth__group--forgot--signup">Don't have an account?</span> Sign up</router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import FormInput from '../../components/FormInput.vue'

    export default {
        components: {
            FormInput
        },
        data () {
            return {
                email: '',
                password: '',
                error: false,
                loading: false,
                type: 'password',
                btnText: 'Show Password',
                remember: true
            };
        },

        computed: {
            loginDisabled () {
                return this.email.length == 0 || this.password.length == 0;
            }
        },

        methods: {

            showPassword() {
                if (this.type === 'password') {
                    this.type = 'text'
                } else {
                    this.type = 'password'
                }
            },
            login() {
                if (this.loginDisabled) return;

                axios.post('/api/login', {
                    email: this.email,
                    password: this.password,
                })
                    .then(response => {

                        if (response.data.passed) {

                            this.remember
                                ? localStorage.setItem('fc-usertoken', response.data.user.api_token)
                                : sessionStorage.setItem('fc-usertoken', response.data.user.api_token)
                            this.error = false;
                            axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.user.api_token


                            window.location.href = '/';

                        } else {

                            this.error = true;
                            localStorage.removeItem('fc-usertoken');
                            sessionStorage.removeItem('fc-usertoken');

                        }

                    })
                    .catch(error => {
                        console.log('ERROR');
                    })
                    .finally(() => this.loading = false);
            }
        },
        mounted () {
            this.$refs.password.focus()
            this.$nextTick(() => this.$refs.email.focus())
        }
    };
</script>
