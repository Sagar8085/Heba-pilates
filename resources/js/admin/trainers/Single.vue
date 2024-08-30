<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">supervisor_account</i>
                        </div>
                        Instructors
                    </h1>
                    <h2 class="page-header__sub">Instructors #{{this.trainer.id}} - {{this.trainer.name}}</h2>
                </div>

                <div class="page-header__col">
                    <button v-if="tab === 'profile'" @click="displayEditModal = true" class="button">
                        Edit <i class="material-icons">edit</i>
                    </button>
                    <button v-if="tab === 'packages'" @click="displayCreatePackageModal = true" class="button">
                        New Package <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <tab-list v-model="tab" :tabs="tabs" />

        <section v-if="tab !== 'availability'" class="page-content">
            <div class="row">
                <div class="columns four-xl four-xxl">
                    <div class="card">
                        <div class="card__title card__title--bold">
                            <img v-if="trainer.trainer && trainer.trainer.avatar" class="card__title__image" :src="trainer.trainer.avatar" alt="Trainer Profile Picture" />
                            <h2>{{ trainer.name }}</h2>
                        </div>
                        <div class="card__body">
                            <div class="card__section">
                                <h3 class="card__subtitle">Profile Information</h3>
                                <dl class="description-list">
                                    <div
                                        v-for="(info, index) in profileInfo"
                                        :key="index"
                                        class="description-list__item">
                                        <dt>{{ info.label }}</dt>
                                        <dd :class="{ 'description-list__item--break': info.label == 'Email' }">{{ info.value }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns eight-xl eight-xxl">
                    <template v-if="tab === 'profile'">

                    </template>
                </div>
            </div>
        </section>

        <div v-if="displayCompletedModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Successfully Updated Trainer</h2>

                <p>Trainer successfully updated</p>

                <div class="modal-alert__buttons">
                    <button class="button button--green" @click="displayCompletedModal = false">Okay!</button>
                </div>
            </div>
        </div>

        <edit-trainer-modal v-on:cancel="displayEditModal = false" :user="trainer" v-on:complete="displayEditModal = false; displayCompletedModal = true; loadtrainer()" :class="displayEditModal ? 'modal modal--active' : 'modal'" />
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import AvailabilityCalendar from './AvailabilityCalendar';
import EditTrainerModal from './EditTrainerModal';
import CreatePackageModal from './CreatePackageModal';
import LineGraph from '../graphs/LineGraph.vue';
import TabList from '../layout/TabList.vue'
import FilterableDataTable from '../layout/FilterableDataTable.vue';
import SessionList from './SessionList.vue';

export default {
    components: {
        AvailabilityCalendar,
        EditTrainerModal,
        CreatePackageModal,
        LineGraph,
        TabList,
        FilterableDataTable,
        SessionList
    },

    data() {
        return {
            id: this.$route.params.id,
            tab: 'profile',
            tabs: [
                { name: 'Profile', key: 'profile' },
            ],
            loading: true,
            trainer: {
                trainer: {
                    biography: "",
                    qualifications: ""
                }
            },

            displayEditModal: false,
            displaySavedModal: false,
            displayCompletedModal: false,
            displayCreatePackageModal: false,

            capacityData: {},
            capacityTotal: 0,
            loadingCapacityData: true,

            KPIs: [],

            tableFilter: 'sessions',
            sessions: [],

            allSessions: [],
            allSessionsPagination: {}
        }
    },

    computed: {
        profileInfo () {
            return [
                {
                    label: 'Email',
                    value: this.trainer.email
                },
                {
                    label: 'Phone number',
                    value: this.trainer.phone_number
                },
                {
                    label: 'Registered',
                    value: this.formatDate(this.trainer.created_at)
                }
            ]
        },
        tableOptions () {
            const cols = {
                packages: {
                    name: 'Name',
                    details: 'Details',
                    price_human: 'Price'
                },
                sessions: {
                    description: 'Description',
                    member: 'Member',
                    package: 'Package',
                    date: 'Date',
                    status: 'Status'
                }
            }

            const rows = { packages: this.trainer.packages || [], sessions: this.trainer.sessions || [] }
            const title =  { packages: 'Packages', sessions: 'Sessions' };
            const searchPlaceholder =  { packages: 'Search Packages', sessions: 'Search Sessions' };

            return {
                filterOptions: [
                    { option: 'Sessions', value: 'sessions' },
                    { option: 'Packages', value: 'packages' }
                ],
                cols: cols[this.tableFilter],
                rows: rows[this.tableFilter],
                title: title[this.tableFilter],
                searchPlaceholder: searchPlaceholder[this.tableFilter]
            }
        }
    },

    mounted() {
        this.loadtrainer();
        this.loadUpcomingSessions();
        this.loadStats();
        this.loadSessionHistory();
        this.loadCapacity();
    },

    methods: {
        /*
         * Fetch the single trainer on page load.
         * @param {none}
         */
        loadtrainer() {
            axios.get('/api/admin/trainers/' + this.$route.params.id).then(response => {
                this.trainer = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        loadUpcomingSessions() {
            axios.get('/api/admin/trainers/' + this.$route.params.id + '/upcoming-sessions').then(response => {
                this.sessions = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        loadSessionHistory(page = 1) {
            axios.get('/api/admin/trainers/' + this.$route.params.id + '/session-history?page=' + page).then(response => {
                this.allSessions = response.data.data;
                this.allSessionsPagination = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        /*
         * Fetch the single trainer stats on page load.
         * @param {none}
         */
        loadStats() {
            axios.get('/api/admin/trainers/' + this.$route.params.id + '/stats').then(response => {
                this.KPIs = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        /*
         * Fetch a trainers capacity over the last 12 months.
         * @param {none}
         */
        loadCapacity() {
            axios.get('/api/admin/trainers/' + this.$route.params.id + '/capacity').then(response => {
                this.capacityData = {
                    labels: response.data.legend,
                    datasets: [
                        {
                            label: 'Capacity',
                            borderColor: '#3392D0',
                            backgroundColor: 'rgba(51,146,208,0.05)',
                            data: response.data.data,
                        }
                    ]
                }

                this.capacityTotal = response.data.sum;
                this.loadingCapacityData = false;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        formatDate (dateString) {
            return moment(dateString).format('DD/MM/YYYY')
        }
    }
}
</script>
