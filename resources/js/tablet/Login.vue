<template>
    <div class="tablet-login">
        <div @click="generateQR()" v-if="qrCodeImage === ''">
            <img class="tablet-login__logo" src="/images/logos/heba-logo-strapline.png">
            <p class="tablet-login__get-started">Tap to get started</p>
            <img class="tablet-login__icon" src="/images/icons/icon-tapToStart.svg">
        </div>

        <div v-if="qrCodeImage !== ''" class="tablet-login__qr">
            <div class="tablet-login__qr-content">
                <h2>To use Heba Pilates in Studio Tablet:</h2>
                <p>1. Open <strong>Heba Pilates</strong> on your phone</p>
                <p>2. Tap <strong>Access</strong> on the bottom of your screen</p>
                <p>3. Tap <strong>Scan a QR Code</strong></p>
                <p>4. Point your phone to this screen to capture the code</p>
            </div>

            <div class="tablet-login__qr-image">
                <div class="tablet-login__qr-image-wrap">
                    <!-- <img src="/images/qr.png"> -->
                    <img :src="'data:image/png;base64, ' + this.qrCodeImage">
                </div>

                <span>SCAN ME</span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            qrCodeIdentifier: '',
            qrCodeImage: '',
            loginChecker: null
        }
    },

    mounted() {
        // axios.defaults.headers.common['Authorization'] = 'Bearer Xo7frxJBZzoAKNSdyNUvoRBZhKpfCypD3NXe9fZP2sfRIpuUzzc0IRufoNe569aSr9mnc7Uf2OUfWRTr0zNSIXBPDzKcL60xpn4FmI88PFP8Fei7mF6J9IkvixU5CknLjXSefB0tnQ8DFUrWk9gujhdfc60ca8c4904562d8e104faf771c150f337af16';
        // localStorage.setItem('tablet-token', 'Xo7frxJBZzoAKNSdyNUvoRBZhKpfCypD3NXe9fZP2sfRIpuUzzc0IRufoNe569aSr9mnc7Uf2OUfWRTr0zNSIXBPDzKcL60xpn4FmI88PFP8Fei7mF6J9IkvixU5CknLjXSefB0tnQ8DFUrWk9gujhdfc60ca8c4904562d8e104faf771c150f337af16')
        // sessionStorage.setItem('tablet-token', 'Xo7frxJBZzoAKNSdyNUvoRBZhKpfCypD3NXe9fZP2sfRIpuUzzc0IRufoNe569aSr9mnc7Uf2OUfWRTr0zNSIXBPDzKcL60xpn4FmI88PFP8Fei7mF6J9IkvixU5CknLjXSefB0tnQ8DFUrWk9gujhdfc60ca8c4904562d8e104faf771c150f337af16')
        // window.location.href = '/tablet';
    },

    methods: {
        generateQR() {
            axios.get('/api/tablet/qr').then(response => {
                console.log(response.data)
                this.qrCodeIdentifier = response.data.identifier;
                this.qrCodeImage = response.data.image;

                var _this = this;
                this.loginChecker = setInterval(function() {
                    console.log('checking qr');

                    axios.get('/api/tablet/qr/' + _this.qrCodeIdentifier).then(response => {
                        console.log(response.data);
                        if (response.data.status === 'accepted') {
                            clearInterval(_this.loginChecker);

                            this.remember
                                ? localStorage.setItem('tablet-token', response.data.user.api_token)
                                : sessionStorage.setItem('tablet-token', response.data.user.api_token);

                            axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.user.api_token

                            window.location.href = '/tablet';
                        }
                    });
                }, 2500);
            });
        }
    }
}
</script>
