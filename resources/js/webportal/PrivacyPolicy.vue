<template>
    <div>
        <div class="navigation">
            <button @click="$router.go(-1)" class="navigation__back"><img src="../../../public/images/icons/backblue.png" style="height: 15px" alt=""> &nbsp; Back</button>
        </div>
        <div class="terms">
            <div class="app-download__terms wrapper">
                <h1 class="app-download__title terms__title">Privacy Policy</h1>
                <!-- <h2 class="app-download__subtitle">To access AF@Home, please read the below terms and conditions and confirm or deny your consent. To use AF@Home or our software you must read and agree to these terms.</h2> -->

                <div class="terms__container">
                    <p class="terms__text">
                        We may collect personal information such as but not limited to your Name, Email Address, Phone Number, Gender, Date of Birth, Company Name, IP Address and Device Information from you on our Sites, Apps or through any other digital or personal communication with you. Personal contact information collected by us is voluntarily provided by you and may be integrated with other information you have provided to our franchisees or partners.
                        <br><br>
                        When voluntarily submitting your data via the forms on our website, you agree that you consent to be contacted by a member of our team.
                        <br><br>
                        Our sites and apps use cookies to help us improve or deliver essential services to you, such as storing little pieces of information in a file called a cookie; for example will allow you to login into and use our services.
                        <br><br>
                        Our sites uses third party cookies from Google Analytics so we can analyse trends of how our services are being used. If you do not wish for cookies to be used by us please close our sites and apps immediately and remove them from your devices.
                        <br><br>
                        We will use your personal information to provide the services we offer on the Sites and through our Apps, including: Processing and updating your membership information, Contacting you regarding services you have engaged in or with, including personal training or collection efforts, Developing and displaying content and advertising tailored to your interests and preferences, Sending you electronic communications you have requested, such as text messages, push notifications, notifications through our Apps, or through your mobile device, Sending you electronic communications you have requested such as emails or push notifications, Contacting you regarding updates to the Sites, Apps, or provide administrative notices to you.
                    </p>
                </div>

                <button class="button button--white terms__button" @click="$router.go(-1)">Back</button>
            </div>
        </div>
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
        },

        methods: {

        }
    };
</script>
