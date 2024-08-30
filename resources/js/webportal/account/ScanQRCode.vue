<template>
  <div>
    <!-- <p class="decode-result">Last result: <b>{{ result }}</b></p> -->

    <qrcode-stream :camera="camera" @decode="onDecode" @init="onInit">
      <div v-if="validationSuccess" class="validation-success">
        You are being logged in...
      </div>

      <div v-if="validationFailure" class="validation-failure">
        This is not a valid QR Code please try again.
      </div>

      <div v-if="validationPending" class="validation-pending">
        Long validation in progress...
      </div>
    </qrcode-stream>
  </div>
</template>

<script>
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'

export default {

  components: { QrcodeStream },

  data () {
    return {
      isValid: undefined,
      camera: 'auto',
      result: null,
    }
  },

  computed: {
    validationPending () {
      return this.isValid === undefined
        && this.camera === 'off'
    },

    validationSuccess () {
      return this.isValid === true
    },

    validationFailure () {
      return this.isValid === false
    }
  },

  methods: {

    onInit (promise) {
      promise
        .catch(console.error)
        .then(this.resetValidationState)
    },

    resetValidationState () {
      this.isValid = undefined
    },

    async onDecode (content) {
      this.result = content
      this.turnCameraOff()

      // pretend it's taking really long
      // await this.timeout(3000)
      // this.isValid = content.startsWith('http')

      axios.post('/api/user/scan-tablet-qr', {
          identifier: content
      }).then(response => {
          console.log(response.data);
          if (response.data.status === 'success') {
              this.isValid = true;
          } else {
              this.isValid = false;
          }
      })

      // some more delay, so users have time to read the message
      await this.timeout(5000)

      if (this.isValid) {
          this.$router.push('/myaccount');
      } else {
          this.turnCameraOn()
      }
    },

    turnCameraOn () {
      this.camera = 'auto'
    },

    turnCameraOff () {
      this.camera = 'off'
    },

    timeout (ms) {
      return new Promise(resolve => {
        window.setTimeout(resolve, ms)
      })
    }
  }
}
</script>

<style scoped>
.validation-success,
.validation-failure,
.validation-pending {
  position: absolute;
  width: 100%;
  height: 100%;

  background-color: rgba(255, 255, 255, .8);
  text-align: center;
  font-weight: bold;
  font-size: 1.4rem;
  padding: 10px;

  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
}
.validation-success {
  color: green;
}
.validation-failure {
  color: red;
}
</style>
