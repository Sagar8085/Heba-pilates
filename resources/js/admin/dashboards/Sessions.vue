<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--dashboard">
                            <i class="material-icons">fitness_center</i>
                        </div>
                        Dashboards
                    </h1>

                    <h2 class="page-header__sub">Sessions and Packages</h2>
                </div>
            </div>
        </section>

        <tab-list v-model="activeTab" :tabs="tabs" @change="updateGraphs" />

        <section class="page-content">
            <div class="row row--equal">
                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>Completed {{ currentTabData.title }}</p>
                        <span class="card__value">{{ currentTabData.stats.completed }}</span>
                        <i class="card__info fas fa-info" @click="openInfo(`Completed ${currentTabData.title}`, `This calendar month, a total of ${currentTabData.stats.completed} ${currentTabData.title} have been completed.`)"></i>
                    </div>
                </div>
                <div class="six three-xl columns">
                    <div class="card">
                        <template v-if="activeTab == 'sessions'">
                            <p class="card__title"><span>{{ currentMonth }}</span>Upcoming {{ currentTabData.title }}</p>
                            <span class="card__value card__value--green">{{ currentTabData.stats.upcoming }}</span>
                        <i class="card__info fas fa-info" @click="openInfo(`Upcoming ${currentTabData.title}`, `This calendar month, there are a total of ${currentTabData.stats.upcoming} upcoming ${currentTabData.title}.`)"></i>
                        </template>
                        <template v-if="activeTab == 'packages'">
                            <p class="card__title"><span>{{ currentMonth }}</span>Active {{ currentTabData.title }}</p>
                            <span class="card__value card__value--green">{{ currentTabData.stats.active }}</span>
                        <i class="card__info fas fa-info" @click="openInfo(`Active ${currentTabData.title}`, `This calendar month, there are a total of ${currentTabData.stats.active} active ${currentTabData.title}.`)"></i>
                        </template>
                    </div>
                </div>
                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>Pending {{ currentTabData.title }}</p>
                        <span class="card__value">{{ currentTabData.stats.pending }}</span>
                        <i class="card__info fas fa-info" @click="openInfo(`Pending ${currentTabData.title}`, `This calendar month, there are a total of ${currentTabData.stats.pending} pending ${currentTabData.title}.`)"></i>
                    </div>
                </div>
                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>Cancelled {{ currentTabData.title }}</p>
                        <span class="card__value card__value--green">{{ currentTabData.stats.cancelled }}</span>
                        <i class="card__info fas fa-info" @click="openInfo(`Cancelled ${currentTabData.title}`, `This calendar month, there have been a total of ${currentTabData.stats.cancelled} cancelled ${currentTabData.title}.`)"></i>
                    </div>
                </div>
            </div>

            <div v-if="currentTabData.graphs" class="row row--equal">
                <div
                    v-for="(graph, i) in currentTabData.graphs"
                    :key="i"
                    :class="{ columns: true, 'six-lg': currentTabData.graphs.length == 2 }">
                    <div class="graph">
                        <p class="graph__title">{{ graph.title }}</p>
                        <span v-if="graph.value" class="graph__value">{{ graph.value }}</span>
                        <div class="graph__container">
                            <component v-if="!graph.loading" ref="graphs" :is="graph.component" :data="graph.data" />
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab == 'sessions'" class="row row--equal">
                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>No Shows</p>
                        <span class="card__value">{{ currentTabData.stats.noshow }}</span>

                        <i class="card__info fas fa-info" @click="openInfo('No Shows', 'Of the ' + sessionsBookedTotal + ' sessions booked this calendar month, ' + currentTabData.stats.noshow + ' were not undertaken.')"></i>
                    </div>
                </div>
                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>Avg. Sessions Per Week</p>
                        <span class="card__value card__value--green">{{ currentTabData.stats.average_sessions }}</span>

                        <i class="card__info fas fa-info" @click="openInfo('Avg. Sessions Per Week', 'The average amount of sessions undertaken per week for this calendar month is ' + currentTabData.stats.average_sessions)"></i>
                    </div>
                </div>
                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>Attendance Rate</p>
                        <span class="card__value">{{ currentTabData.stats.attendance }}%</span>

                        <i class="card__info fas fa-info" @click="openInfo('Attendance Rate', currentTabData.stats.attendance + '% of all your sessions planned to be conducted this calendar month were undertaken, the rest were marked as noshow.')"></i>
                    </div>
                </div>
                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>Average Rating</p>
                        <span class="card__value card__value--green">{{ currentTabData.stats.rating }} / 5</span>

                        <i class="card__info fas fa-info" @click="openInfo('Average Rating', 'Out of all the sessions conducted this month, the average rating given by all of your members across all trainers is ' + currentTabData.stats.rating + ' stars out of 5.')"></i>
                    </div>
                </div>
            </div>

            <!-- <div class="row row--equal">
                <div class="six-xl columns">
                    <div class="graph">
                        <p class="graph__title">Sessions Booked</p>
                        <span class="graph__value graph__value--green">{{ this.sessionsBookedTotal }}</span>
                        <line-graph :data="sessionsBookedData" v-if="!loadingSessionsBookedData" />
                    </div>
                </div>

                <div class="six-xl columns">
                    <div class="graph">
                        <p class="graph__title">Session Cancellations</p>
                        <span class="graph__value">{{ this.sessionsCancelledTotal }}</span>
                        <line-graph :data="sessionsCancelledData" v-if="!loadingSessionsCancelledData" />
                    </div>
                </div>
            </div> -->
        </section>

        <div v-if="infoModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">{{ this.infoModalTitle }}</h2>

                <p>{{ this.infoModalMessage }}</p>

                <div class="modal-alert__buttons">
                    <button class="button button--outlines" @click="infoModal = false">Okay</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import LineGraph from './../graphs/LineGraph.vue';
import PieGraph from './../graphs/PieGraph.vue';
import TabList from '../layout/TabList.vue';

export default {
    components: {
        LineGraph,
        PieGraph,
        TabList
    },

    data() {
        return {
            currentMonth: moment().format('MMMM'),
            infoModal: false,
            infoModalTitle: '',
            infoModalMessage: '',

            stats: {},

            loadingSessionsBookedData: true,
            sessionsBookedTotal: 0,
            sessionsBookedData: {},

            loadingSessionsCancelledData: true,
            sessionsCancelledTotal: 0,
            sessionsCancelledData: {},

            sessionsDurationSplitData: {
                labels: ['30 Minutes', '45 Minutes', '60 Minutes', '120 Minutes'],
                datasets: [{
                    data: [40, 23, 24, 13],
                    backgroundColor: ['#4DC398', '#17865D', '#9C88FF', '#6517FF']
                }]
            },
            packageTypeSplitData: {
                labels: ['x5 30 min Sessions', 'x3 45 min Sessions', 'x5 60 min Sessions', 'x10 120 min Sessions'],
                datasets: [{
                    data: [40, 23, 24, 13],
                    backgroundColor: ['#4DC398', '#17865D', '#9C88FF', '#6517FF']
                }]
            },
            packagesActiveData: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Members with Active Packages',
                        borderColor: '#3392D0',
                        backgroundColor: 'rgba(51,146,208,0.05)',
                        data: [99, 95, 90, 77, 80, 89, 87, 96, 91, 90, 88, 95],
                    }
                ]
            },
            packagesActiveTotal: 1203,

            tabs: [
                { name: 'Sessions', key: 'sessions' },
                { name: 'Packages', key: 'packages' }
            ],
            activeTab: 'sessions',
            sessionStats: {
                completed: 0, upcoming: 0, pending: 0, cancelled: 0,
                noshow: 0, average_sessions: 0, attendance: 0, rating: 0
            },
            packageStats: { completed: 1234, active: 231, pending: 4, cancelled: 2 }
        }
    },
    computed: {
        currentTabData () {
            return this.tabData[this.activeTab];
        },
        tabData () {
            return {
                sessions: {
                    title: 'Sessions',
                    stats: this.sessionStats,
                    graphs: [
                        {
                            title: 'Session Bookings',
                            value: this.sessionsBookedTotal,
                            data: this.sessionsBookedData,
                            loading: this.loadingSessionsBookedData,
                            component: LineGraph
                        },
                        {
                            title: 'Duration Split',
                            data: this.sessionsDurationSplitData,
                            loading: false,
                            component: PieGraph
                        }
                    ]
                },
                packages: {
                    title: 'Packages',
                    stats: this.packageStats,
                    graphs: [
                        {
                            title: 'Members with Active Packages',
                            value: this.packagesActiveTotal,
                            data: this.packagesActiveData,
                            loading: false,
                            component: LineGraph
                        },
                        {
                            title: 'Package Type Split',
                            data: this.packageTypeSplitData,
                            loading: false,
                            component: PieGraph
                        }
                    ]
                }
            }
        }
    },

    mounted() {
        this.loadStats();
        this.loadSessionsBookedGraph();
        // this.loadSessionsCancelledGraph();
    },

    methods: {
        updateGraphs () {
            this.$nextTick(() => {
                this.$refs.graphs.map(x => x.rerenderChart());
            })
        },

        /*
         * Load top level stats for cards.
         * @param {none}
         */
        loadStats() {
            axios.get('/api/admin/dashboard/sessions/stats').then(response => {
                this.sessionStats = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            });
        },

        loadSessionsBookedGraph() {
            axios.get('/api/admin/dashboard/sessions/bookings').then(response => {
                this.sessionsBookedData = {
                    labels: response.data.legend,
                    datasets: [
                        {
                            label: 'Sessions Booked',
                            borderColor: '#4DC398',
                            backgroundColor: 'rgba(77,195,152,0.05)',
                            data: response.data.data,
                        }
                    ]
                };

                this.sessionsBookedTotal = response.data.sum;
                this.loadingSessionsBookedData = false;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            });
        },

        loadSessionsCancelledGraph() {
            axios.get('/api/admin/dashboard/sessions/cancellations').then(response => {
                this.sessionsCancelledData = {
                    labels: response.data.legend,
                    datasets: [
                        {
                            label: 'Sessions Cancelled',
                            borderColor: '#6517FF',
                            backgroundColor: 'rgba(101,23,255, 0.05)',
                            data: response.data.data,
                        }
                    ]
                };

                this.sessionsCancelledTotal = response.data.sum;
                this.loadingSessionsCancelledData = false;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            });
        },

        openInfo(title, message) {
            this.infoModal = true;
            this.infoModalTitle = title;
            this.infoModalMessage = message;
        }
    }
}
</script>
