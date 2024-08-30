<template>
    <div class="booking-card" @click="view()">
        <div class="booking-card__content">
            <p :class="`booking-card__status booking-card__status--${status}`">
                {{ status }}
            </p>
            <h3 class="booking-card__title">{{ booking.type == 'package' ? booking.package.name : booking.time_human }}</h3>
            <p class="booking-card__subtitle">{{ sessionText }}</p>
        </div>
        <img class="booking-card__image" :src="booking.trainer.profile.avatar" alt="Trainer Image" />
    </div>
</template>

<script>
import moment from 'moment';

export default {
    props: {
        booking: Object
    },
    computed: {
        sessionText () {
            return this.booking.type == 'session' ?
                `${this.booking.length_human} Minute Session` :
                `${this.booking.credits}x ${this.booking.session_length} Minute ${this.booking.credits > 1 ? 'Sessions' : 'Session' }`;
        },
        status () {
            return this.booking.type == 'package' && this.booking.active ? 'active' : this.booking.status;
        }
    },
    methods: {
        formatDate (date) {
            return moment(date).format('D MMM')
        },

        view() {
            console.log('test');
            let emit = this.$emit('viewBooking', this.booking);
            console.log(emit);
        }
    }
}
</script>
