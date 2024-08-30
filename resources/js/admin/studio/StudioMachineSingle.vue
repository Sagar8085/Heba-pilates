<template>
    <div v-if="selectedTimeslot == null">
        <!-- <PageHeader :title="machine.name + ' Reservations'" :subtitle="studio.name" icon="event">
            <button class="button" @click="showEditModal = true">Edit {{ machine.name }}</button>
        </PageHeader> -->
        <!-- <tab-list active="machines" :tabs="tabs" /> -->

            <timeslot-table
                v-if="!loading"
                :data="pagination.data"
                :pagination="pagination"
                weekly
                :machine_id="this.machine_id"
                :operating-hours="this.operatingHours"
                @select-available="bookTimeslot"
                @select-booked="goToBooking"
                @nextPage="getTimeslots(1, 'next')"
                @previousPage="getTimeslots(1, 'previous')">
                <AdminInput :value="machine_id" type="select" @input="goToMachine">
                    <option v-for="item in studio.reformers" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </AdminInput>
            </timeslot-table>

        <MachineModal
            v-if="showEditModal"
            :studio="studio"
            :editData="machine"
            @close="closeEditModal" />
    </div>
    <CreateReservation
        v-else
        :timeslot="selectedTimeslot"
        :machine="machine"
        :studio="studio"
        @back="selectedTimeslot = null"
        @reserved="reservationPlaced" />
</template>

<script>
import AdminInput from '../layout/AdminInput.vue'
import PageHeader from '../layout/PageHeader.vue'
import TabList from '../layout/TabList.vue'
import TimeslotTable from './TimeslotTable.vue'
import MachineModal from './MachineModal.vue'
import CreateReservation from './CreateReservation.vue'
import moment from 'moment'
import axios from 'axios'

export default {
    props: {
        studio_id: String,
        operatingHours: Object,
    },
    components: {
        PageHeader,
        TabList,
        AdminInput,
        TimeslotTable,
        MachineModal,
        CreateReservation
    },
    data () {
        return {
            machine: {},
            studio: {},
            pagination: {
                date_start: null,
                date_end: null,
                dates: {}
            },
            showEditModal: false,
            selectedTimeslot: null,
            loading: true,
            machine_id: 1
        }
    },
    watch: {
        async '$route.params' () {
            await this.fetchData();
        }
    },
    methods: {
        goToBooking ({ id }) {
            this.$router.push({
                name: 'ViewReservation',
                params: {
                    machine_id: this.machine.id,
                    studio_id: this.studio.id,
                    booking_id: id
                }
            })
        },
        reservationPlaced () {
            this.selectedTimeslot = null;
            this.getTimeslots();
        },
        bookTimeslot () {
            console.log('go to new booking page')
            // this.selectedTimeslot = timeslot || {
            //     date_human: 'Wed 31 March 2021',
            //     time_human: '9am'
            // }
        },
        closeEditModal () {
            this.showEditModal = false;
            this.getMachine();
        },
        goToMachine (id) {
            this.machine_id = id;
            this.getTimeslots();
            // this.$router.push({
            //     name: 'StudioMachineSingle',
            //     params: {
            //         studio_id: this.$route.params.studio_id,
            //         machine_id: id
            //     }})
        },
        getMachine () {
            this.machine = {
                id: this.machine_id,
                name: 'Nuforma #' + this.machine_id,
                location: 'Room 1',
                status: 'active'
            }
        },
        getStudio () {
            axios.get('/api/admin/gyms/' + this.studio_id).then(response => {
                this.studio = response.data;
                this.machine_id = response.data.reformers[0].id;
            });
        },
        getTimeslots (page = 1, paginationType = null) {
            console.log('getting single machine reservations')
            axios.get('/api/admin/gyms/' + this.studio_id + '/machine/' + this.machine_id + '/reservations?date_start=' + this.pagination.date_start + '&date_end=' + this.pagination.date_end + '&paginationType=' + paginationType).then(response => {
                this.pagination = {
                    current_page: page,
                    last_page: 2,
                    data: response.data.data,
                    date_start: response.data.date_start,
                    date_end: response.data.date_end,
                    dates: response.data.dates
                };
                this.loading = false;
            });
        },
        async fetchData () {
            await Promise.all([ this.getStudio(), this.getMachine() ]);
            await this.getTimeslots();
        }
    },
    async mounted () {
        await this.fetchData();
    }
}
</script>
