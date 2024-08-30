<template>
    <div>
        <div class="modal modal--active" v-if="this.$route.params.status === 'success'">
            <div class="modal__box modal__box--small modal__body">
                <h2 class="modal__title">Purchase Complete</h2>

                <p>Your payment has been accepted, thanks for using Heba Pilates!</p>

                <div class="modal__buttons">
                    <router-link to="/on-demand" class="button button--outline">View Library</router-link>
                    <router-link :to="'/on-demand/video/' + this.$route.params.video_id" class="button button--green">Watch Class</router-link>
                </div>
            </div>
        </div>

        <div class="modal modal--active" v-else>
            <div class="modal__box modal__box--small modal__body">
                <h2 class="modal__title">Purchase Cancelled</h2>

                <p>Oh dear, it looks like your payment didn't go through or you changed your mind. Well.. you know where we are if you redecide!</p>

                <div class="modal__buttons">
                    <router-link to="/on-demand" class="button button--outline">View Library</router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    },

    mounted() {
        if (this.$route.params.status === 'success') {
            axios.post('/api/ondemand/' + this.$route.params.video_id + '/purchase', {
                stripe_order_id: this.$route.query.stripe_order_id
            }).then(response => {
                console.log(response.data);
            }).catch(error => {
                console.log('ERROR');
            });
        }
    }
}
</script>
