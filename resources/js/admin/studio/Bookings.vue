<template>
<div>
    <PageHeader title="Studio Reservations" subtitle="A visual calendar style view of bookings can be found within a specific studios profile." icon="event" />
    <tab-list v-model="tab" :tabs="[ 'Upcoming', 'All Bookings' ]" />
    <div class="page-content">
        <section v-if="tab === 'Upcoming'">

        <div class="filters">
            <div class="filters__placeholder" @click="loadUpcomingBookings(false, upcomingPagination.current_page)"><i :class="loading ? 'fas fa-sync-alt fa-spin' : 'fas fa-sync-alt'"></i></div>
            <div class="filters__placeholder" @click="downloading = true; loadUpcomingBookings(true, upcomingPagination.current_page)"><i :class="loading ? 'fas fa-cloud-download-alt' : 'fas fa-cloud-download-alt'"></i></div>
        </div>

        <div class="table-list table-list--top">
            <div class="table-list__header">
                <h3><span v-if="upcomingPagination.from">{{ upcomingPagination.from + ' - ' + upcomingPagination.to }} of </span>{{ upcomingPagination.total }} Bookings</h3>

                <div class="table-list__header-pagination">
                    <span :class="{'material-icons-outlined': true, 'disabled': upcomingPagination.current_page === 1}" @click="change_page('previous')">navigate_before</span>
                    <span :class="{'material-icons-outlined': true, 'disabled': upcomingPagination.current_page === upcomingPagination.last_page}" @click="change_page('next')">navigate_next</span>
                </div>
            </div>

            <div class="table-list__scroll">
                <table class="table-list__table">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Gym</th>
                            <th>Machine</th>
                            <th>Booking Date</th>
                            <th>Booking Time</th>
                            <th>Booking Created</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(upcomingReservation, index) in upcomingReservations" :key="index" :class="{'strike-through': upcomingReservation.deleted_at === null ? false : true}">
                            <td><router-link :to="'/admin/members/' + upcomingReservation.user_id">{{ upcomingReservation.member.name }}</router-link></td>
                            <td><router-link :to="'/admin/gyms/' + upcomingReservation.gym_id">{{ upcomingReservation.gym_name }}</router-link></td>
                            <td>{{ upcomingReservation.reformer.name }}</td>
                            <td>{{ upcomingReservation.date_formatted }}</td>
                            <td>{{ upcomingReservation.time_human }}</td>
                            <td>{{ upcomingReservation.created_at_human }}</td>
                            <td v-if="upcomingReservation.deleted_at === null"><i class="far fa-trash-alt colour--red cursor--pointer" @click="confirmDeleteBooking(upcomingReservation.id, index)"></i></td>
                            <td v-else>Cancelled <span v-if="upcomingReservation.deleter && upcomingReservation.deleter !== null">by {{ upcomingReservation.deleter.name }}</span><br>{{ upcomingReservation.deleted_at_human }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <section v-if="tab === 'All Bookings'">
        <div class="filters">
            <div class="filters__placeholder" @click="toggleFilterDropdown()"><i class="fas fa-filter"></i></div>
            <div class="filters__placeholder" @click="loadBookings(false, pagination.current_page)"><i :class="loading ? 'fas fa-sync-alt fa-spin' : 'fas fa-sync-alt'"></i></div>
            <div class="filters__placeholder" @click="downloading = true; loadBookings(true, pagination.current_page)"><i :class="loading ? 'fas fa-cloud-download-alt' : 'fas fa-cloud-download-alt'"></i></div>

            <div :class="filterDropdown ? 'filters__dropdown filters__dropdown--active' : 'filters__dropdown'">
                <div class="row">
                    <div class="twelve columns">

                        <h3>Studios</h3>
                        <div class="row">
                            <div class="twelve columns">
                                <multiselect v-if="gyms.length > 0" v-model="selectedGyms" :options="gyms" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose one 1 more studios" label="name" track-by="id" :preselect-first="false"></multiselect>
                            </div>
                        </div>

                        <h3>Reservation Date</h3>
                        <div class="row">
                            <div class="four columns">
                                <div class="filters__dropdown-item">
                                    <label for="allDates">All time</label>
                                    <input id="allDates" type="checkbox" value="" v-model="allDates">
                                </div>
                            </div>

                            <div class="four columns" :class="allDates ? 'form-element--blur' : ''">
                                <datepicker :value="start_date" @selected="this.select_start" :format="'yyyy-MM-dd'"></datepicker>
                            </div>

                            <div class="four columns" :class="allDates ? 'form-element--blur' : ''">
                                <datepicker :value="end_date" @selected="this.select_end" :format="'yyyy-MM-dd'"></datepicker>
                            </div>
                        </div>

                        <h3>Created On</h3>
                        <div class="row">
                            <div class="four columns">
                                <div class="filters__dropdown-item">
                                    <label for="createdOnAllDates">All time</label>
                                    <input id="createdOnAllDates" type="checkbox" value="" v-model="createdOnAllDates">
                                </div>
                            </div>

                            <div class="four columns" :class="createdOnAllDates ? 'form-element--blur' : ''">
                                <datepicker :value="createdOnStartDate" @selected="this.selectCreatedOnStartDate" :format="'yyyy-MM-dd'"></datepicker>
                            </div>

                            <div class="four columns" :class="createdOnAllDates ? 'form-element--blur' : ''">
                                <datepicker :value="createdOnEndDate" @selected="this.selectCreatedOnEndDate" :format="'yyyy-MM-dd'"></datepicker>
                            </div>
                        </div>

                        <button style="margin-top: 1.5rem;" class="button button--full" @click="filterDropdown = false; loadBookings(false, 1)">Apply Filters</button>

                    </div>
                </div>
            </div>
        </div>

        <div class="table-list table-list--top">
            <div class="table-list__header">
                <h3>{{ pagination.from + ' - ' + pagination.to }} of {{ pagination.total }} Bookings</h3>

                <div class="table-list__header-pagination">
                    <span :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === 1}" @click="change_page('previous')">navigate_before</span>
                    <span :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === pagination.last_page}" @click="change_page('next')">navigate_next</span>
                </div>
            </div>

            <div class="table-list__scroll">
                <table class="table-list__table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Gym</th>
                            <th>Machine</th>
                            <th>Booking Date</th>
                            <th>Booking Time</th>
                            <th>Booking Created</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(reservation, index) in reservations" :key="index" :class="{'strike-through': reservation.deleted_at === null ? false : true}">
                            <td><router-link :to="'/admin/members/' + reservation.user_id">{{ reservation.member.name }}</router-link></td>
                            <td><router-link :to="'/admin/gyms/' + reservation.gym_id">{{ reservation.gym_name }}</router-link></td>
                            <td>{{ reservation.reformer.name }}</td>
                            <td>{{ reservation.date_formatted }}</td>
                            <td>{{ reservation.time_human }}</td>
                            <td>{{ reservation.created_at_human }}</td>
                            <td v-if="reservation.deleted_at === null"><i class="far fa-trash-alt colour--red cursor--pointer" @click="confirmDeleteBooking(reservation.id, index)"></i></td>
                            <td v-else>Cancelled <span v-if="reservation.deleter && reservation.deleter !== null">by {{ reservation.deleter.name }}</span><br>{{ reservation.deleted_at_human }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

        <modal v-model="showDeleteBookingConfirmationModal" hideClose hideCancel>
            <template>
                <span class="modal__title">Are you sure you wish to delete this booking?</span>

                <div class="modal__buttons">
                    <button class="button" @click="showDeleteBookingConfirmationModal = false">No, Cancel</button>
                    <button class="button button--red" @click="deleteBooking">Yes, Delete</button>
                </div>
            </template>
        </modal>
    </div>
</div>
</template>

<script>
import axios from 'axios'
import PageHeader from '../layout/PageHeader.vue'
import Datepicker from 'vuejs-datepicker';
import Modal from '../../components/Modal.vue';
import TabList from '../layout/TabList.vue';

export default {
    components: { PageHeader, Datepicker, Modal,TabList },

    data () {
        return {
            gyms: [],
            selectedGyms: [],
            tab: 'Upcoming',

            reservations: [],
            upcomingReservations: [],
            pagination: {},
            upcomingPagination: {},

            start_date: '',
            end_date: '',
            filterDropdown: false,

            allDates: true,
            createdOnAllDates: true,
            createdOnStartDate: '',
            createdOnEndDate: '',

            loading: true,
            downloading: false,

            showDeleteBookingConfirmationModal: false,
            bookingToDelete: null,
            bookingToDeleteRef: null
        }
    },

    mounted() {
        var date = new Date();
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        this.start_date = date.getFullYear() + '-' + month + '-01';
        this.end_date = date.getFullYear() + '-' + month + '-' + ("0" + date.getDate()).slice(-2);


        var date = new Date();
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        this.createdOnStartDate = date.getFullYear() + '-' + month + '-01';
        this.createdOnEndDate = date.getFullYear() + '-' + month + '-' + ("0" + date.getDate()).slice(-2);

        this.loadGyms();
        this.loadBookings();
        this.loadUpcomingBookings();
    },

    methods: {
        loadGyms() {
            axios.get('/api/admin/gyms').then(response => {
                this.gyms = response.data;
            });
        },

        loadBookings(download = false, page = 1) {
            this.loading = true;

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.post('/api/admin/gyms/reservations?page=' + page + '&download=' + download, {
                gyms: this.selectedGyms,
                allDates: this.allDates,
                startDate: this.start_date,
                endDate: this.end_date,
                createdOnAllDates: this.createdOnAllDates,
                createdOnStartDate: this.createdOnStartDate,
                createdOnEndDate: this.createdOnEndDate

            }).then(response => {
                if (download) {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');

                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'Studio Reservations.csv');

                    document.body.appendChild(fileLink);

                    fileLink.click();
                    this.downloading = false;
                    this.loading = false;
                } else {
                    this.reservations = response.data.data;
                    this.pagination = response.data;
                    this.loading = false;
                }
            });
        },

        loadUpcomingBookings(download = false, page = 1) {
            this.loading = true;

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.post('/api/admin/gyms/reservations/upcoming?page=' + page + '&download=' + download, {
                gyms: this.selectedGyms,
                allDates: this.allDates,
                startDate: this.start_date,
                endDate: this.end_date,
                createdOnAllDates: this.createdOnAllDates,
                createdOnStartDate: this.createdOnStartDate,
                createdOnEndDate: this.createdOnEndDate

            }).then(response => {
                console.log(response);
                if (download) {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');

                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'Studio Reservations.csv');

                    document.body.appendChild(fileLink);

                    fileLink.click();
                    this.downloading = false;
                    this.loading = false;
                } else {
                    this.upcomingReservations = response.data.data;
                    this.upcomingPagination = response.data;
                    this.loading = false;
                }
            });
        },

        change_page(type) {
            this.loading = true;

            if (type === 'previous') {
                this.loadBookings(false, (this.pagination.current_page - 1));

            } else {
                if (this.pagination.current_page !== this.pagination.last_page) {
                    this.loadBookings(false, (this.pagination.current_page + 1));

                }
            }

            if (type === 'previous') {
                this.loadUpcomingBookings(false, (this.upcomingPagination.current_page - 1));
            } else {
                if (this.upcomingPagination.current_page !== this.upcomingPagination.last_page) {
                    this.loadUpcomingBookings(false, (this.upcomingPagination.current_page + 1));
                }
            }

            this.scrollToTop();
        },

        select_start: function(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.start_date = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        select_end: function(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.end_date = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        toggleFilterDropdown() {
            if (this.filterDropdown) {
                this.filterDropdown = false;
            } else {
                this.filterDropdown = true;
            }
        },

        selectCreatedOnStartDate(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.createdOnStartDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectCreatedOnEndDate(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.createdOnEndDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        confirmDeleteBooking(booking_id, ref) {
            this.bookingToDelete = booking_id;
            this.bookingToDeleteRef = ref;
            this.showDeleteBookingConfirmationModal = true;
        },

        deleteBooking() {
            axios.delete('/api/admin/gyms/reservations/' + this.bookingToDelete).then(response => {
                alert('Booking Deleted');
                this.reservations.splice(this.bookingToDeleteRef, 1);
                this.bookingToDelete = null;
                this.bookingToDeleteRef = null;
                this.showDeleteBookingConfirmationModal = false;
            })
        }
    }
}
</script>
