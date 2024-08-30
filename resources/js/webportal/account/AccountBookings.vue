<template>
     <AccountPageWrapper>
        <section class="account__section">
            <h1>Bookings</h1>
            <div v-if="(bookings.length > 0 || reservations.length > 0) && !loading">
                <ul class="booking-tabs">
                    <!-- <li :class="tab === 'live' ? 'active' : ''" @click="tab = 'live'">Live Classes</li> -->
                    <li :class="tab === 'studio' ? 'active' : ''" @click="tab = 'studio'">Studio Reservations</li>
                </ul>

                <ul class="booking-list" v-if="tab === 'live'">
                    <li v-for="booking in bookings" :key="booking.id" class="horizontal-card horizontal-card--bordered horizontal-card--border-red horizontal-card--5-cols">
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Date</label>
                            <p class="horizontal-card__section__value horizontal-card__section__value--large">
                                {{ booking.date_human }}
                                <menu-dropdown
                                    :value="shownCalendarMenu == booking.id"
                                    @input="$event ? shownCalendarMenu = booking.id : shownCalendarMenu = null">
                                    <button class="button button--icon button--transparent button--plain">
                                        <i class="far fa-calendar-plus" />
                                    </button>

                                    <template slot="items">
                                        <li class="menu-dropdown__item">
                                            <a :href="addToCalendar(booking, 'google')" class="menu-dropdown__link" target="_blank" @click="shownCalendarMenu = null">
                                                Add to Google Calendar
                                            </a>
                                        </li>
                                        <li class="menu-dropdown__item">
                                            <button @click="addToCalendar(booking, 'apple')" class="menu-dropdown__link">
                                                Add to Apple Calendar
                                            </button>
                                        </li>
                                    </template>
                                </menu-dropdown>
                            </p>
                        </div>
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Class Name</label>
                            <p class="horizontal-card__section__value">{{ booking.category.name }}</p>
                        </div>
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Duration</label>
                            <p class="horizontal-card__section__value">{{ booking.duration }} mins</p>
                        </div>
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Start Time</label>
                            <p class="horizontal-card__section__value horizontal-card__section__value--large">
                                {{ booking.time_human }}
                            </p>
                        </div>
                        <div class="horizontal-card__section" v-if="!booking.reformer">
                            <button class="button button--outline" @click="openCancelModal(booking)">Cancel</button>
                            <a class="button" :href="'/live/' + booking.id + '/stream'">Join</a>
                        </div>
                        <div class="horizontal-card__section" v-else>
                            <label class="page-subtitle">Type</label>
                            <p class="horizontal-card__section__value">In Studio</p>
                        </div>
                    </li>
                    <li v-if="bookings.length == 0">
                        <p class="account-bookings__no-data">You have no upcoming live class bookings.</p>
                        <router-link class="button" to="/live-classes">Browse Live Classes</router-link>
                    </li>
                </ul>

                <ul class="booking-list" v-if="tab === 'studio'">
                    <li v-for="booking in reservations" :key="booking.id" class="horizontal-card horizontal-card--bordered horizontal-card--border-orange horizontal-card--4-cols horizontal-card--no-button">
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Date</label>
                            <p class="horizontal-card__section__value horizontal-card__section__value--large">
                                {{ booking.date_human }}
                            </p>
                        </div>
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Studio</label>
                            <p class="horizontal-card__section__value">{{ booking.reformer.gym.name }}</p>
                        </div>
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Duration</label>
                            <p class="horizontal-card__section__value">60 mins</p>
                        </div>
                        <div class="horizontal-card__section">
                            <label class="page-subtitle">Start Time</label>
                            <p class="horizontal-card__section__value horizontal-card__section__value--large">
                                {{ booking.time_human }}
                            </p>
                        </div>
                        <div class="horizontal-card__section">
                            <button class="button button--outline" @click="openCancelModal(booking)">Cancel</button>
                        </div>
                    </li>
                    <li v-if="reservations.length == 0">
                        <p class="account-bookings__no-data">You have no studio reservations.</p>
                        <router-link class="button" to="/studio">Make a reservation</router-link>
                    </li>
                </ul>
            </div>
            <loading-spinner
                v-else
                class="account__buttons"
                :loading="loading"
                loadingText="bookings"
                :no-data="bookings.length == 0 && reservations.length == 0">

                <template slot="no-data">
                    <p class="account-bookings__no-data">You’ve not got any upcoming bookings or reservations</p>
                    <!-- <router-link class="button" to="/live-classes">Browse Live Classes</router-link> -->
                    <router-link class="button" to="/studio">Make a reservation</router-link>
                </template>

            </loading-spinner>
        </section>
        <Modal v-model="showCancelModal" title="Are you sure you want to cancel?" hideCancel @close="closeCancelModal">
            Cancellations within 1 hour of the start time are non-refundable in accordance with our <router-link to="/terms">Cancellation Policy</router-link>
            <template slot="buttons">
                <button class="button button--outline button--full" @click="closeCancelModal">Go Back</button>
                <button class="button button--full" @click="cancelReservation">Confirm Cancellation</button>
            </template>
        </Modal>
     </AccountPageWrapper>
</template>

<script>
import AccountPageWrapper from './AccountPageWrapper.vue';
import Modal from '../../components/Modal.vue';
import MenuDropdown from '../../components/MenuDropdown.vue';
import axios from 'axios'
import moment from 'moment'
import { GoogleCalendar, ICalendar } from 'datebook';
import LoadingSpinner from '../../tablet/layout/LoadingSpinner.vue';

export default {
    components: { AccountPageWrapper, Modal, MenuDropdown, LoadingSpinner },

    props: { authUser: Object },

    data () {
        return {
            bookings: [],
            reservations: [],
            showCancelModal: false,
            selectedBooking: null,
            tab: 'studio',
            shownCalendarMenu: null,
            loading: true
        }
    },

    mounted() {
        this.loadBookings();
        this.loadReservations();
    },

    methods: {
        loadBookings() {
            axios.get('/api/live/bookings').then(response => {
                this.bookings = response.data;
                this.loading = false;
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        loadReservations() {
            axios.get('/api/my/reservations').then(response => {
                this.reservations = response.data;
            }).catch(error => {
                console.log('ERROR', error);
            });
        },

        openCancelModal (booking) {
            this.showCancelModal = true;
            this.selectedBooking = booking;
        },
        closeCancelModal () {
            this.showCancelModal = false;
            this.selectedBooking = null;
        },
        cancelBooking () {
            // Hook in API to cancel this.selectedBooking
            axios.delete('/api/live/class/' + this.selectedBooking.id + '/cancel').then(response => {
                this.closeCancelModal();
                this.loadBookings();
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },
        cancelReservation () {
            // Hook in API to cancel this.selectedBooking
            axios.delete('/api/reservations/' + this.selectedBooking.id).then(response => {
                this.closeCancelModal();
                this.loadReservations();
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },
        addToCalendar (booking, calendarKey) {
            const startDate = moment(booking.datetime);
            const endDate = moment(booking.datetime).add(booking.duration, 'minutes');
            const path = 'https://sandbox.Hebapilates.olivex.co.uk'
            const link = path + '/live/' + booking.id + '/stream';

            const config = {
                title: booking.category.name,
                location: link,
                description: link + '\n\n' + booking.category.description,
                start: startDate.toDate(),
                end: endDate.toDate()
            }

            if (calendarKey == 'google') {
                const googleCalendar = new GoogleCalendar(config)

                return googleCalendar.render()
            }
            else if (calendarKey == 'apple') {
                const icalendar = new ICalendar(config)

                this.shownCalendarMenu = null;

                return icalendar.download(booking.category.name + ' - ' + booking.datetime + '.ics')
            }
        }
    }
}
</script>
