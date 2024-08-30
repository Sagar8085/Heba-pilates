<template>
    <div class="account-page--qr">
        <div v-if="qrCodeImage !== ''" class="tablet-login__qr tablet-login__qr--center">
            <div class="tablet-login__qr-content">
                <h2>Your Heba Membership QR Code</h2>
                <p>1. Make sure your screen brightness is high</p>
                <p>2. Hold phone face down above the scanner</p>
                <p>3. Enter the studio</p>

                <span style="margin-top: 4rem;"><router-link class="button" to="/myaccount">Back</router-link></span>
            </div>

            <div class="tablet-login__qr-image">
                <img :src="'data:image/png;base64, ' + this.qrCodeImage">
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    data() {
        return {
            qrCodeImage: '',
            qrCodeIdentifier: ''
        }
    },

    mounted() {
        this.generateQR();
    },

    methods: {
        generateQR() {
            axios.get('/api/account/qr').then(response => {
                console.log(response.data)
                this.qrCodeIdentifier = response.data.identifier;
                this.qrCodeImage = response.data.image;
            });
        }
    }
}
</script>
