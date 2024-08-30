<template>
    <div class="tablet-login">
        <div class="tablet-login__row code-login">
            <div class="tablet-login__qr-content tablet-login__col">
                <h2>To generate an access code:</h2>
                <p>1. Open <strong>Heba Pilates</strong> on your phone</p>
                <p>2. Tap <strong>Access</strong> on the bottom of your screen</p>
                <p>3. Tap <strong>Generate an Access Code</strong></p>
                <p>4. Tap the code display on your phone into the keypad</p>
                <router-link :to="'/tablet'">Login using QR code</router-link>
            </div>

            <div class="tablet-login__col">
                <div class="number-output"> 
                    <span  v-if="this.numbers.length > 0">{{ numbers }}</span> 
                    <span  v-if="this.numbers.length == 0">Enter Code</span> 
                </div>
                    <div class="keypad">
                        <div class="keys" @click="addCode('1');">1</div>
                        <div class="keys" @click="addCode('2');">2</div>
                        <div class="keys" @click="addCode('3');">3</div>
                        <div class="keys" @click="addCode('4');">4</div>
                        <div class="keys" @click="addCode('5');">5</div>
                        <div class="keys" @click="addCode('6');">6</div>
                        <div class="keys" @click="addCode('7');">7</div>
                        <div class="keys" @click="addCode('8');">8</div>
                        <div class="keys" @click="addCode('9');">9</div>

                    </div>
                    <div class="button button--outline" v-if="this.numbers.length > 0" @click="clearCode();">Clear</div>
                    <div class="button">Submit</div>

              

                
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            numbers:[],
        }
    },

    mounted() {
      
    },

    methods: {
        addCode(number){

       
            this.numbers += number;
            console.log(this.numbers);
        },
        clearCode(){
            this.numbers = '';
        },
        enterCode(){
            axios.post('/api/app/login-code', {
                email: this.studioOrHome,
                gym_id: this.studio,
                paymentType: type,
                creditPackPurchaseId: creditPackPurchaseId
            }).then(response => {
                console.log('got response')
                console.log(response)
                if (response.data.status === 'failure') {
                    this.bookingFailure = true;
                    this.bookingFailureMessage = response.data.message;
                    return;
                } else {
                    this.bookingSuccess = true;
                }
            }).catch(error => {
                console.log('ERROR', error);
                this.processingBooking = false;
            }).finally(() => {
                this.processingBooking = false;
            });
        }
    }
}
</script>
