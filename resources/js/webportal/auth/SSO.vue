<template>
    <div class="wrapper">
        <section class="section section--hero section--bottom-divider">
            <div class="section__main">
                <h1 class="section__title">Signing you in...</h1>

                <p class="section__text">We are automatically signing you in, one moment please.</p>
            </div>

            <div class="section__side">
                <img src="/images/member-portal/secure.svg">
            </div>
        </section>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data () {
            return {
            };
        },

        mounted() {
            localStorage.setItem('fc-usertoken', this.$route.params.token);

            var _this = this;

            setTimeout(function() {
                if (_this.$route.query.to === 'freebooking') {
                    window.location.href = '/elites/vpt/booking?type=free';
                } else if (_this.$route.query.to === 'manage_subscription') {
                    window.location.href = '/elites/vpt/subscription';
                } else if (_this.$route.query.to === 'manage_payg') {
                    window.location.href = '/elites/vpt/payg';
                } else {
                    window.location.href = '/elites/vpt/home';
                }
            }, 2500);
        },

        methods: {
        }
    };
</script>
