<template>
    <div v-if="selectedTimeslot == null">
        <PageHeader title="Studio Reservations" :subtitle="studio.name" icon="event" />
        <tab-list active="upcoming" :tabs="tabs" />

        <div class="page-content">
            <timeslot-table
                :data="pagination.data"
                :pagination="pagination"
                v-if="!loading"
                @select-available="bookTimeslot"
                @select-booked="goToBooking"/>
        </div>
    </div>
    <CreateReservation
        v-else
        :timeslot="selectedTimeslot"
        :machine="selectedMachine"
        :studio="studio"
        @back="resetSelectedTimeslot"
        @reserved="reservationPlaced" />
</template>

<script>
import Pagination from '../../components/Pagination.vue'
import PageHeader from '../layout/PageHeader.vue'
import TabList from '../layout/TabList.vue'
import TimeslotTable from './TimeslotTable.vue'
import CreateReservation from './CreateReservation.vue'
import axios from 'axios'

export default {
    components: { PageHeader, TabList, TimeslotTable, Pagination, CreateReservation },
    data () {
        return {
            activeTab: 'upcoming',
            tabs: [
                {
                    key: 'upcoming', name: 'Todays Reservations',
                    link: `/admin/studio/${this.$route.params.studio_id}`
                },
                {
                    key: 'machines', name: 'Nurforma Machines',
                    link: `/admin/studio/${this.$route.params.studio_id}/machines`
                }
            ],
            studio: {},
            pagination: {},
            selectedTimeslot: null,
            selectedMachine: null,
            loading: true
        }
    },
    watch: {
        '$route.params' () {
            this.getStudio();
        }
    },
    methods: {
        resetSelectedTimeslot () {
            this.selectedTimeslot = null;
            this.selectedMachine = null;
        },
        goToBooking ({ booking, machine }) {
            this.$router.push({
                name: 'ViewReservation',
                params: {
                    machine_id: machine.id,
                    studio_id: this.studio.id,
                    booking_id: booking.id
                }
            })
        },
        reservationPlaced () {
            this.resetSelectedTimeslot();
            this.getTimeslots();
        },
        bookTimeslot ({ timeslot, machine }) {
            this.selectedTimeslot = timeslot || {
                date_human: 'Wed 31 March 2021',
                time_human: '6am'
            };
            this.selectedMachine = machine;
        },
        getStudio () {
            this.studio = {
                id: this.$route.params.studio_id,
                name: 'Studio ' + this.$route.params.studio_id,
                machines: [
                    { id: 1, name: 'Nuforma #1' },
                    { id: 2, name: 'Nuforma #2' },
                    { id: 3, name: 'Nuforma #3' }
                ]
            }

            this.getTimeslots();
        },
        getTimeslots () {
            // this.pagination = {
            //     date_human: 'Wed 31 March',
            //     current_page: 1,
            //     last_page: 2,
            //     data: [
            //         {
            //             id: 1,
            //             name: 'Nuforma #1',
            //             timeslots: {
            //                 6: {
            //                     available: false,
            //                     date_human: 'Wed 31 March 2021',
            //                     time_human: '6am',
            //                     booking: {
            //                         id: 1,
            //                         member: {
            //                             name: 'Sophie Popter'
            //                         }
            //                     }
            //                 },
            //                 7: {
            //                     available: true,
            //                     date_human: 'Wed 31 March 2021',
            //                     time_human: '7am',
            //                     member: null,
            //                 },
            //                 8: {
            //                     available: false,
            //                     date_human: 'Wed 31 March 2021',
            //                     time_human: '8am',
            //                     booking: {
            //                         id: 2,
            //                         member: {
            //                             name: 'Sophie Popter'
            //                         }
            //                     }
            //                 }
            //             }
            //         },
            //         { id: 2, name: 'Nuforma #2', timeslots: {
            //             10: {
            //                 available: false,
            //                 date_human: 'Wed 31 March 2021',
            //                 time_human: '10am',
            //                 booking: {
            //                     id: 3,
            //                     member: {
            //                         name: 'Sophie Popter'
            //                     }
            //                 }
            //             }
            //         } },
            //         { id: 3, name: 'Nuforma #3', timeslots: {} }
            //     ]
            // }

            axios.get('/api/admin/gyms/' + this.$route.params.studio_id + '/machine-reservations').then(response => {
                this.pagination = {
                    date_human: response.data.date,
                    current_page: 1,
                    last_page: 2,
                    data: response.data.bookings
                }
                this.loading = false;
            });
        }
    },
    mounted () {
        this.getStudio();
    }
}
</script>
