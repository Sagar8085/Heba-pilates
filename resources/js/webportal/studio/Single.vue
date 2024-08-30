<template>
    <div class="wrapper page-content">
        <loading-spinner :loading="loading" :noData="!studio.id" noDataText="Studio not found." />

        <template v-if="!loading && studio.id">

            <studio-booking-confirmed
                v-if="step == 3"
                :studio="studio"
                :bookingDetails="bookingDetails" />

            <template v-else>
                <breadcrumbs>
                    <li class="breadcrumbs__item">
                        <router-link class="breadcrumbs__item__link" to="/studio">Reserve Studio Time</router-link>
                    </li>
                    <li class="breadcrumbs__item">
                        <span class="breadcrumbs__item__link" @click="backToStep1">Select Date, Time and Equipment</span>
                    </li>
                    <li v-if="step == 2" class="breadcrumbs__item">
                        <span class="breadcrumbs__item__link">Reservation Summary</span>
                    </li>
                </breadcrumbs>

                <div class="row">
                    <div class="columns eight-lg">

                        <select-timeslot
                            v-if="step == 1"
                            :date.sync="bookingDetails.date"
                            :equipment.sync="bookingDetails.equipment"
                            @select="selectTimeslot" />

                        <reservation-summary
                            v-else-if="step == 2"
                            :bookingDetails="bookingDetails"
                            :authUser="authUser"
                            @book="book" />

                    </div>
                    <div class="columns four-lg studio__location">
                        <google-map class="studio__map" :markers="[ studio.position ]" />

                        <h3>Studio location</h3>

                        <p class="studio__location__address">
                            {{ studio.name }}
                            <br>
                            {{ studio.address }}
                        </p>

                        <p><strong>Call:</strong> {{ studio.phone_number }}</p>
                        <p><strong>Email:</strong> {{ studio.email }}</p>

                        <h3 class="opening">Opening Times</h3>
                        <table class="opening_table">
                            <tr>
                                <td>Monday</td>
                                <td>
                                    <div v-if="this.studio.operating_hours.monday[0]">
                                    {{this.studio.operating_hours.monday[0]}} to {{this.studio.operating_hours.monday[1]}}
                                    </div>
                                    <div v-else>
                                        Closed
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Tuesday</td>
                                <td>
                                    <div v-if="this.studio.operating_hours.tuesday[0]">
                                    {{this.studio.operating_hours.tuesday[0]}} to {{this.studio.operating_hours.tuesday[1]}}
                                    </div>
                                    <div v-else>
                                        Closed
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Wednesday</td>
                                <td>
                                    <div v-if="this.studio.operating_hours.wednesday[0]">
                                    {{this.studio.operating_hours.wednesday[0]}} to {{this.studio.operating_hours.wednesday[1]}}
                                    </div>
                                    <div v-else>
                                        Closed
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Thursday</td>
                                <td>
                                    <div v-if="this.studio.operating_hours.thursday[0]">
                                    {{this.studio.operating_hours.thursday[0]}} to {{this.studio.operating_hours.thursday[1]}}</div>
                                    <div v-else>
                                        Closed
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Friday</td>
                                <td>
                                    <div v-if="this.studio.operating_hours.friday[0]">
                                    {{this.studio.operating_hours.friday[0]}} to {{this.studio.operating_hours.friday[1]}}</div>
                                    <div v-else>
                                        Closed
                                    </div> 
                                </td>
                            </tr>
                            <tr>
                                <td>Saturday</td>
                                <td>   
                                    <div v-if="this.studio.operating_hours.saturday[0]">
                                    {{this.studio.operating_hours.saturday[0]}} to {{this.studio.operating_hours.saturday[1]}}</div>
                                    <div v-else>
                                       Closed
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Sunday</td>
                                <td>
                                    <div v-if="this.studio.operating_hours.sunday[0]">
                                    {{this.studio.operating_hours.sunday[0]}} to {{this.studio.operating_hours.sunday[1]}}
                                    </div>
                                    <div v-else>
                                        Closed
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <h3 class="opening" v-if="this.customOpening">Custom Opening Times</h3>
                        <table class="opening_table">
                            <tr v-for="(item, index) in customOpening" :key="index">
                                <td>{{ formatDate(item.date) }}</td>
                                <td>
                                    <span v-if="item.opening_time">
                                        {{ item.opening_time }} to {{ item.closing_time }} 
                                    </span>
                                    <span v-if="!item.opening_time">
                                        Closed
                                    </span>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </template>
        </template>
    </div>
</template>

<script>
import Breadcrumbs from '../../components/Breadcrumbs.vue'
import SelectTimeslot from './SelectTimeslot.vue'
import ReservationSummary from './ReservationSummary.vue'
import StudioBookingConfirmed from './StudioBookingConfirmed.vue'
import GoogleMap from '../../components/GoogleMap.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'
import moment from 'moment'


export default {
    props: { authUser: Object },

    components: {
        Breadcrumbs,
        SelectTimeslot,
        ReservationSummary,
        StudioBookingConfirmed,
        GoogleMap,
        LoadingSpinner
    },

    mounted() {
        this.loadStudio();
        this.loadCustomOpening();
    },

    data () {
        return {
            step: 1,
            bookingDetails: {
                date: '',
                equipment: '',
                timeslot: null,
            },
            studio: {},
            loading: false,
            customOpening: {},
        }
    },
    methods: {
        formatDate (dateString) {
            return moment(String(dateString)).format('Do MMM')
        },
        scrollToTop() {
            document.getElementById('app').scrollIntoView();
        },
        backToStep1 () {
            if (this.step == 1) return;

            this.step = 1;
            this.bookingDetails.timeslot = null;
            this.scrollToTop();
        },
        selectTimeslot (slot) {
            this.step = 2;
            this.bookingDetails.timeslot = slot;
            this.scrollToTop();
        },
        book () {
            // Booking is done within child compontent.
            this.step = 3;
            this.scrollToTop();
        },

        loadStudio() {
            axios.get('/api/gyms/' + this.$route.params.id).then(response => {
                this.studio = response.data;

            });
        },
        loadCustomOpening() {
            axios.get('/api/gyms/' + this.$route.params.id+'/custom-opening-times').then(response => {
                this.customOpening = response.data.opening;
            });
        }
    }
}
</script>
