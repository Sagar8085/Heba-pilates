<template>
<div>
    <PageHeader title="Member Profile" :subtitle="'Member #' + this.$route.params.id + ' - All Studio Reservations'" icon="assignment_ind" />

    <section class="back">
        <router-link :to="'/admin/members/' + this.$route.params.id">
            <img src="/images/icons/backblue.png" alt="Back"> &nbsp; Back to Member Profile
        </router-link>
    </section>

    <div class="page-content">
        <div class="table-list">
            <div class="table-list__header">
                <h3>{{ reservations.length }} Bookings</h3>
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
                            <td>{{ reservation.date_human }}</td>
                            <td>{{ reservation.time_human }}</td>
                            <td>{{ reservation.created_at_human }}</td>
                            <td v-if="reservation.deleted_at === null"><i class="far fa-trash-alt colour--red cursor--pointer" @click="confirmDeleteBooking(reservation.id, index)"></i></td>
                            <td v-else>Cancelled <span v-if="reservation.deleter && reservation.deleter !== null">by {{ reservation.deleter.name }}</span><br>{{ reservation.deleted_at_human }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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

export default {
    components: { PageHeader, Datepicker, Modal },

    data () {
        return {
            gyms: [],
            selectedGyms: [],

            reservations: [],
            pagination: {},

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
    },

    methods: {
        loadGyms() {
            axios.get('/api/admin/gyms').then(response => {
                this.gyms = response.data;
            });
        },

        loadBookings(download = false, page = 1) {
            this.loading = true;

            axios.get('/api/admin/members/' + this.$route.params.id + '/reservations/all').then(response => {
                this.reservations = response.data;
                this.loading = false;
            });
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
