<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">assignment_ind</i>
                        </div>
                        Gyms
                    </h1>
                    <h2 class="page-header__sub">Gym #{{ this.gym.id }} - {{ this.gym.name }}</h2>
                </div>
                <div class="page-header__col">
                    <button @click="displayCustomModal = true" class="button">
                        Set Custom Opening Hours
                        <i class="material-icons">add_circle</i>
                    </button>

                    <button @click="displayOpenModal = true" class="button">
                        Set Opening Hours
                        <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <opening-times-modal  v-bind:gym="gym" v-on:cancel="displayOpenModal = false" v-on:complete="displayOpenModal = false" :class="displayOpenModal ? 'modal modal--active' : 'modal'" />
        <custom-opening-times-modal  v-bind:gym="gym" v-on:cancel="displayCustomModal = false" v-on:complete="displayCustomModal = false" :class="displayCustomModal ? 'modal modal--active' : 'modal'" />

        <section class="page-content">
            <div class="row">
                <div class="twelve columns">
                    <div class="card">
                        <div class="card__title card__title--bold">
                            <h2>{{ this.gym.name }}</h2>
                        </div>

                        <div class="card__body">
                            <div class="row">
                                <div class="six columns">
                                    <div class="card__section">
                                        <h3 class="card__subtitle">Contact Information</h3>
                                        <dl class="description-list">
                                            <div class="description-list__item">
                                                <dt>Phone Number</dt>
                                                <dd>{{ this.gym.phone_number }}</dd>
                                            </div>

                                            <div class="description-list__item">
                                                <dt>Email Address</dt>
                                                <dd>{{ this.gym.email }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                <div class="six columns">
                                    <div class="card__section">
                                        <h3 class="card__subtitle">Address</h3>
                                        <dl class="description-list">
                                            <div class="description-list__item">
                                                <dt>Street Address</dt>
                                                <dd>{{ this.gym.street_address }}</dd>
                                            </div>

                                            <div class="description-list__item">
                                                <dt>City</dt>
                                                <dd>{{ this.gym.city }}</dd>
                                            </div>

                                            <div class="description-list__item">
                                                <dt>Postcode</dt>
                                                <dd>{{ this.gym.postcode }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="tab tab--none tab--bottom">
                <ul>
                    <li
                        :class="tab === 'dayView' ? 'active' : ''"
                        @click="switchTab('dayView')"
                    >
                        <a>Daily Schedule</a>
                    </li>
                    <li
                        :class="tab === 'weekView' ? 'active' : ''"
                        @click="switchTab('weekView')"
                    >
                        <a>7 Day Schedule</a>
                    </li>
                </ul>
            </section>

            <div v-if="tab === 'dayView' && !loading && bookingTable.machines.length > 0">
                <timeslot-table-container-daily
                    :current-date="bookingTable.currentDate"
                    :gym-id="gym.id"
                    :machines="bookingTable.machines"
                    @nextPage="loadNextDay"
                    @previousPage="loadPreviousDay"
                    @jump-to-weekly="jumpToMachineView"
                    @set-date-to-today="setDateToToday"
                    @block-out-machine="blockOutMachine"
                    @free-up-machine="freeUpMachine"
                >
                    <template v-slot:timeslotTable>
                        <timeslot-table
                            :table-data="dailyBookingTableData"
                            @select-available="selectAvailable"
                            @block-out-timeslot="blockOutTimeslot"
                            @free-up-timeslot="freeUpTimeslot"
                        />
                    </template>
                </timeslot-table-container-daily>
            </div>

            <div v-else-if="tab === 'weekView' && !loading && bookingTable.machines.length > 0">
                <timeslot-table-container-weekly
                    :dates="weeklyBookingDates"
                    @nextPage="loadNextWeek"
                    @previousPage="loadPreviousWeek"
                    @jump-to-daily="jumpToDayView"
                    @set-date-to-today="setDateToToday"
                    @block-out-day="blockOutDate"
                    @free-up-day="freeUpDate"
                >
                    <template v-slot:machineList>
                        <AdminInput :value="bookingTable.currentMachine" type="select" @input="changeMachine">
                            <option v-for="machine in bookingTable.machines" :key="machine.id" :value="machine.id">
                                {{ machine.name }}
                            </option>
                        </AdminInput>
                    </template>

                    <template v-slot:timeslotTable>
                        <timeslot-table
                            :table-data="weeklyBookingTableData"
                            @select-available="selectAvailable"
                            @block-out-timeslot="blockOutTimeslot"
                            @free-up-timeslot="freeUpTimeslot"
                        >
                        </timeslot-table>
                    </template>

                </timeslot-table-container-weekly>
            </div>

            <div v-else-if="!loading">
                No machines configured for this gym.
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import TimeslotTable from './../studio/TimeslotTable.vue'
import TimeslotTableContainerDaily from '../studio/TimeslotTableContainerDaily';
import TimeslotTableContainerWeekly from '../studio/TimeslotTableContainerWeekly';
import OpeningTimesModal from './OpeningTimesModal.vue';
import CustomOpeningTimesModal from './CustomOpeningTimesModal.vue';
import AdminInput from '../layout/AdminInput';

export default {
    name: 'AdminGymSingle',

    components: {
        TimeslotTable,
        TimeslotTableContainerDaily,
        TimeslotTableContainerWeekly,
        OpeningTimesModal,
        CustomOpeningTimesModal,
        AdminInput,
    },

    data() {
        return {
            gym: {
                reformers: []
            },
            pagination: {},
            tab: 'dayView',
            loading: true,
            machine_id: 0,
            displayOpenModal: false,
            displayCustomModal: false,
            bookingTable: {
                machines: [],
                data: {},
                /** @var {moment.Moment} */
                currentDate: moment(),
                /** @var {number} */
                currentMachine: 1,
            },
        }
    },

    computed: {
        dailyBookingTableData() {
            const currentDate = this.bookingTable.currentDate.format('YYYY-MM-DD');

            if (this.tab === 'weekView' || !Object.keys(this.bookingTable.data).includes(currentDate)) {
                return {};
            }

            return this.bookingTable.data[this.bookingTable.currentDate.format('YYYY-MM-DD')];
        },

        weeklyBookingTableData() {
            if (this.tab === 'dayView') {
                return {};
            }

            const currentDate = this.bookingTable.currentDate.format('YYYY-MM-DD');
            const endDate = this.bookingTable.currentDate.clone().add(6, 'days').format('YYYY-MM-DD');
            const availableDates = Object.keys(this.bookingTable.data);

            if (!availableDates.includes(currentDate) || !availableDates.includes(endDate)) {
                return {};
            }

            // To create the weekly table we need to pivot the array of daily
            // tables.

            // data is our pivot table. We'll start by creating it as {timeslot: [], ...}
            const data = {};
            const dates = this.getDatesForWeek(this.bookingTable.currentDate)
                .map(date => date.format('YYYY-MM-DD'));

            Object.keys(this.bookingTable.data[dates[0]]).forEach(timeslot => data[timeslot] = []);

            // Now we need to iterate over each date. For each date, we iterate
            // the timeslots, then we pull out the machine ID that matches
            // this.bookingTable.currentMachine and add it to our pivot table.
            dates.forEach(
                date =>
                    Object.keys(data).forEach(
                        timeslot => {
                            const contents = this.bookingTable.data[date][timeslot].filter(
                                ({ machineId}) => machineId === this.bookingTable.currentMachine
                            ).pop();

                            data[timeslot].push(contents);
                        }
                    ),
            );

            return data;
        },

        weeklyBookingDates() {
            return this.getDatesForWeek(this.bookingTable.currentDate);
        },
    },

    mounted() {
        this.initialise();
    },

    activated() {
        this.initialise();
    },

    methods: {
        initialise() {
            if (this.gym.id === undefined || parseInt(this.$route.params.id, 10) !== this.gym.id) {
                this.tab = 'dayView';
                this.bookingTable.machines = [];
                this.bookingTable.data = {};
                this.loadGym().then(() => this.loadDataAround(moment()));
            }
        },

        startLoading() {
            this.loading = true;
        },

        finishLoading() {
            this.loading = false;
        },

        reload() {
            this.bookingTable.data = {};
            return this.loadDataAround(this.bookingTable.currentDate);
        },

        getDatesForWeek(startDate) {
            return [
                ...Array(7).keys()
            ].map(
                count => startDate.clone().add(count, 'days'),
            );
        },

        /**
         * @param {string} to "dayView" or "weekView"
         */
        switchTab(to) {
            this.loadDataAround(this.bookingTable.currentDate)
                .then(() => this.tab = to);
        },

        loadGym() {
            return axios.get(`/api/admin/gyms/${this.$route.params.id}`)
                .then(({ data }) => this.gym = data)
                .catch(error => {
                    if(error.response.status === 403) {
                        this.$router.push('/admin/permission-denied');
                    }
                });
        },

        /**
         * @param {moment.Moment} startDate
         * @param {moment.Moment} endDate
         */
        loadBookings(startDate, endDate) {
            const url = `/api/admin/gyms/${this.$route.params.id}/machine-reservations`;
            const start = `start=${startDate.format('YYYY-MM-DD')}`;
            const end = `end=${endDate.format('YYYY-MM-DD')}`;

            return axios.get(`${url}?${start}&${end}`).then(response => response.data);
        },

        /**
         * @param {object} bookingData
         */
        parseBookingData(bookingData) {
            const day = bookingData[Object.keys(bookingData)[0]];

            this.bookingTable.machines = day[Object.keys(day)[0]]
                .map(
                    ({ machineId }) => this.gym.reformers.filter(reformer => reformer.id === machineId).pop(),
                );

            if (this.bookingTable.machines[0] !== undefined) {
                this.bookingTable.currentMachine = this.bookingTable.machines[0].id;
                this.bookingTable.data = bookingData;
            }
        },

        /**
         * @param {string|undefined} date
         * @param {string} timeslot
         * @param {number} machineId
         */
        selectAvailable(date, timeslot, machineId) {
            if (date === undefined) {
                date = this.bookingTable.currentDate.format('YYYY-MM-DD');
            }

            const timeKey = moment(timeslot, 'HH:mm:ss').format('Hmm');

            this.$router.push(
                `/admin/studio/${this.$route.params.id}/machines/${machineId}/booking?timekey=${timeKey}&date=${date}`
            );
        },

        /**
         * @param {string} timeslot
         * @param {object[]} rowData
         */
        blockOutTimeslot(timeslot, rowData) {
            const bookings = rowData.filter(({ bookingStatus }) => bookingStatus.status === 'booking');
            const nonBookings = rowData.filter(({ bookingStatus }) => bookingStatus.status !== 'booking');

            if (nonBookings.length === 0) {
                alert('There are no dates in this timeslot that do not already have bookings.');
                return;
            }

            if (bookings.length > 0) {
                if (!confirm(`There are ${bookings.length} bookings in this timeslot that will not be affected. Continue?`)) {
                    return;
                }
            }

            Promise.all(
                nonBookings.map(
                    ({ date, machineId }) => {
                        const startAndEnd = `${date}T${timeslot}`;

                        return axios.post(
                            `/api/admin/gyms/${this.gym.id}/machine/${machineId}/block/${startAndEnd}/${startAndEnd}`
                        );
                    },
                ),
            )
                .then(this.reload)
                .catch(({ response }) => {
                    switch (response.status) {
                        case 422:
                            alert(response.data);
                            break;
                    }
                });
        },

        /**
         * @param timeslot
         * @param rowData
         */
        freeUpTimeslot(timeslot, rowData) {
            Promise.all(
                rowData
                .filter(({ bookingStatus }) => bookingStatus.status === 'blockedOut')
                .map(
                    ({ date, machineId }) => {
                        const startAndEnd = `${date}T${timeslot}`;

                        return axios.post(
                            `/api/admin/gyms/${this.gym.id}/machine/${machineId}/free/${startAndEnd}/${startAndEnd}`
                        );
                    },
                ),
            ).then(this.reload);
        },

        /**
         * @param {number} machineId
         * @param {moment.Moment} date
         */
        blockOutMachine(machineId, date) {
            const start = date.format('YYYY-MM-DDT00:00:00');
            const end = date.format('YYYY-MM-DDT23:59:59');

            axios.post(`/api/admin/gyms/${this.gym.id}/machine/${machineId}/block/${start}/${end}`)
                .then(this.reload)
                .catch(({ response }) => {
                    switch (response.status) {
                        case 422:
                            alert(response.data);
                            break;
                    }
                });
        },

        /**
         * @param {number} machineId
         * @param {moment.Moment} date
         */
        freeUpMachine(machineId, date) {
            const start = date.format('YYYY-MM-DDT00:00:00');
            const end = date.format('YYYY-MM-DDT23:59:59');

            axios.post(`/api/admin/gyms/${this.gym.id}/machine/${machineId}/free/${start}/${end}`)
                .then(this.reload);
        },

        /**
         * @param {string} date
         */
        blockOutDate(date) {
            this.blockOutMachine(this.bookingTable.currentMachine, moment(date));
        },

        /**
         * @param {string} date
         */
        freeUpDate(date) {
            this.freeUpMachine(this.bookingTable.currentMachine, moment(date));
        },
        
        setDateToToday() {
            this.loadDataAround(moment());
        },

        /**
         * @param {number} machineId
         */
        changeMachine(machineId) {
            this.bookingTable.currentMachine = machineId;
        },

        /**
         * @param {string} date
         */
        jumpToDayView(date) {
            this.switchTab('dayView');
            this.bookingTable.currentDate = moment(date);
        },

        /**
         * @param {number} machineId
         */
        jumpToMachineView(machineId) {
            this.changeMachine(machineId);
            this.switchTab('weekView');
        },

        /**
         * @param {moment.Moment} date
         * @returns {Promise<void>}
         */
        loadDataAround(date) {
            const currentlyLoadedDates = Object.keys(this.bookingTable.data);

            if (
                currentlyLoadedDates.includes(date.format('YYYY-MM-DD'))
                && currentlyLoadedDates.includes(date.clone().add(6, 'days').format('YYYY-MM-DD'))
            ) {
                // We already have these dates.
                this.bookingTable.currentDate = date.clone();
                return Promise.resolve();
            }

            this.startLoading();

            const loadDaysBefore = 14;
            const loadDaysAfter = 20;

            return this.loadBookings(
                date.clone().subtract(loadDaysBefore, 'days'),
                date.clone().add(loadDaysAfter, 'days')
            )
                .then(this.parseBookingData)
                .then(() => this.bookingTable.currentDate = date.clone())
                .then(this.finishLoading);
        },

        loadNextDay() {
            this.loadDataAround(this.bookingTable.currentDate.clone().add(1, 'days'));
        },

        loadNextWeek() {
            this.loadDataAround(this.bookingTable.currentDate.clone().add(7, 'days'));
        },

        loadPreviousDay() {
            this.loadDataAround(this.bookingTable.currentDate.clone().subtract(1, 'days'));
        },

        loadPreviousWeek() {
            this.loadDataAround(this.bookingTable.currentDate.clone().subtract(7, 'days'));
        },
    }
}
</script>
