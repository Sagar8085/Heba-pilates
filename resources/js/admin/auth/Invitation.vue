<template>
    <div>
        <section class="auth">
            <div class="auth__main auth__main--black">
                <div>
                    <img class="auth__logo" src="/images/logos/heba-logo-strapline.png" alt="Volution" title="Volution">

                    <form class="auth__form">
                        <label class="auth__label auth__label--error" v-if="this.error">Please enter a password longer than 6 characters.</label>
                        <label class="auth__label auth__label--error" v-if="this.failure">Either your token is invalid or has already been used.</label>

                        <div class="auth__element">
                            <label class="auth__label">Set a Password</label>
                            <input class="auth__input" type="password" v-model="password">
                        </div>

                        <div class="auth__element">
                            <button type="button" class="auth__button" @click="this.setPassword">Complete Setup</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data () {
            return {
                email: '',
                password: '',
                error: false,
                failure: false
            };
        },

        mounted() {
            console.log('Hello Login');
        },

        methods: {
            setPassword() {
                axios.post('/api/admin/invitation', {
                    token: this.$route.params.token,
                    password: this.password
                })
                    .then(response => {

                        if (response.data.status === 'success') {

                            if (response.data.user.role_id === 1 || response.data.user.role_id === 2) {
                                this.error = false;
                                localStorage.setItem('fc-admin-usertoken', response.data.user.api_token);

                                window.location.href = '/admin';
                            }

                            else {
                                this.error = false;
                                localStorage.setItem('fc-usertoken', response.data.user.api_token);

                                window.location.href = '/download';
                            }

                        } else {

                            this.error = false;
                            this.failure = true;
                            localStorage.removeItem('fc-admin-usertoken');
                            localStorage.removeItem('fc-usertoken');

                        }

                        console.log('Has Data');
                        console.log(response);

                    })
                    .catch(error => {

                        this.error = true;
                        console.log('ERROR');
                        console.log(error)

                    })
                    .finally(() => this.loading = false);
            }
        }
    };
</script>
