<template>
    <div>
        <section class="studio__section studio__section--large">
            <h1>Booking Summary</h1>

            <div class="horizontal-card horizontal-card--4-cols horizontal-card--no-button horizontal-card--no-margin">
                <div class="horizontal-card__section">
                    <label class="horizontal-card__section__title">Time</label>
                    <span class="horizontal-card__section__value horizontal-card__section__value--large">{{ bookingDetails.timeslot.time_human }}</span>
                </div>

                <div class="horizontal-card__section">
                    <label class="horizontal-card__section__title">Date</label>
                    <span class="horizontal-card__section__value horizontal-card__section__value--large">{{ bookingDetails.timeslot.date_human }}</span>
                </div>

                <div class="horizontal-card__section">
                    <label class="horizontal-card__section__title">Duration</label>
                    <span class="horizontal-card__section__value horizontal-card__section__value--large">{{ bookingDetails.timeslot.duration }} mins</span>
                </div>
            </div>
        </section>

        <section class="studio__section">
            <h3>Reserve Studio Time</h3>

            <div class="membership account__section">
                <ul class="booking-list" v-if="this.authUser.subscription !== null">
                    <li :class="{ 'horizontal-card': true, 'horizontal-card--no-button horizontal-card--2-cols': authUser.subscription.studio_credits == 0, 'horizontal-card--3-cols': authUser.subscription.studio_credits > 0 }">
                        <div class="horizontal-card__section">
                            <h4 class="page-subtitle">My Membership</h4>
                            <p class="horizontal-card__section__value horizontal-card__section__value--large">
                                {{ this.authUser.subscription.name }}
                            </p>
                        </div>
                        <div class="horizontal-card__section">
                            <h4 class="page-subtitle">In-Studio Credits Remaining</h4>
                            <p class="horizontal-card__section__value">{{ this.authUser.subscription.studio_credits_human }}</p>
                        </div>
                        <div class="horizontal-card__section" v-if="this.authUser.subscription.studio_credits > 0">
                            <button class="button button--outline" @click="bookReservation('membership')">Book Now (1 Credit)</button>
                        </div>
                    </li>
                </ul>

                <ul>
                    <section class="account__section membership__credit-packs">
                        <h3 class="page-subtitle">My Active Credit Packs</h3>

                        <ul class="booking-list">
                            <li class="booking-list__booking" v-for="(creditPack, index) in this.creditPackPurchases">
                                <div class="booking-list__booking__section">
                                    <h4 class="page-subtitle">Type</h4>
                                    <p class="booking-list__booking__section__value booking-list__booking__section__value--large">{{ creditPack.pack.name }}</p>
                                </div>
                                <div class="booking-list__booking__section">
                                    <h4 class="page-subtitle">In-Studio Credits</h4>
                                    <p class="booking-list__booking__section__value">{{ creditPack.studio_credits }} Remaining</p>
                                </div>
                                <div class="booking-list__booking__section">
                                    <button class="button button--outline" @click="bookReservation('credit', creditPack.id)" v-if="creditPack.studio_credits > 0">Book (1 Credit)</button>
                                </div>
                            </li>
                        </ul>
                    </section>

                    <li class="booking-list__booking">
                        <div class="booking-list__booking__section">
                            <h4 class="page-subtitle">Additional Credit Packs</h4>
                            <p class="booking-list__booking__section__value booking-list__booking__section__value--large">
                                Need additional credits?
                            </p>
                        </div>
                        <div class="booking-list__booking__section">
                            <router-link class="button button--outline" to="/membership">Purchase Credits</router-link>
                        </div>
                    </li>
                </ul>
                <li style="margin: 2rem 0;">You can view our <router-link to="/terms">Cancellation</router-link> and <router-link to="/terms">Refund Policy</router-link> Terms and Conditions at any time.</li>
            </div>

            <!-- <div class="horizontal-card horizontal-card--bordered horizontal-card--3-cols">
                <div class="horizontal-card__section">
                    <label class="horizontal-card__section__title">Credit Pack</label>
                    <span class="horizontal-card__section__value horizontal-card__section__value--large">10 Visits</span>
                </div>

                <div class="horizontal-card__section">
                    <label class="horizontal-card__section__title">Remaining Visits</label>
                    <span class="horizontal-card__section__value horizontal-card__section__value--large">3</span>
                </div>

                <div class="horizontal-card__section">
                    <button class="button" @click="$emit('book')">Book Now (1 Credit)</button>
                </div>
            </div> -->
        </section>

    </div>
</template>

<script>
export default {
    props: { bookingDetails: Object, authUser: Object },

    data() {
        return {
            creditPackPurchases: []
        }
    },

    mounted() {
        console.log('summary mounted')
        console.log(this.authUser)

        this.loadCreditPackPurchases();
    },

    methods: {
        /*
         * Load all credit packs this user has purchased.
         * @param {none}
         */
        loadCreditPackPurchases() {
            axios.get('/api/account/my-credit-packs').then(response => {
                this.creditPackPurchases = response.data;
            });
        },

        bookReservation(type, creditPackPurchaseId = null) {
            axios.post('/api/gyms/' + this.$route.params.id + '/reservations/timeslots', {
                paymentType: type,
                creditPackPurchaseId: creditPackPurchaseId,
                datetime: this.bookingDetails.timeslot.datetime
            }).then(response => {
                this.$emit('book');
            });
        }
    }
}
</script>
