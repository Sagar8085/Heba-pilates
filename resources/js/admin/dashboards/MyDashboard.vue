<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">dashboard_customize</i>
                        </div>
                        Dashboards
                    </h1>

                    <h2 class="page-header__sub">Main Dashboard</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <h1 style="font-size: 2rem; margin-bottom: 1rem;">Welcome to your Dashboard!</h1>
            <h2 style="font-size: 1rem; margin-bottom: 2rem;">What would you like to see/do?</h2>

            <div class="row">
                <div class="four columns">
                    <div class="card">
                        <p class="card__title"><span>Door Access</span>Live Activity Log</p>
                        <span class="card__value" style="margin-bottom: 1rem; height: 40px;"><i class="material-icons" style="font-size: 2rem;">sensor_door</i></span>
                        <router-link class="button" to="/admin/dashboard/door-access">View Door Access Log</router-link>
                    </div>
                </div>

                <div class="four columns">
                    <div class="card">
                        <p class="card__title"><span>Users</span>Manage Admins</p>
                        <span class="card__value" style="margin-bottom: 1rem; height: 40px;"><i class="material-icons" style="font-size: 2rem;">admin_panel_settings</i></span>
                        <router-link class="button" to="/admin/admins">Manage Admins</router-link>
                    </div>
                </div>

                <div class="four columns">
                    <div class="card">
                        <p class="card__title"><span>Users</span>Manage Members</p>
                        <span class="card__value" style="margin-bottom: 1rem; height: 40px;"><i class="material-icons" style="font-size: 2rem;">assignment_ind</i></span>
                        <router-link class="button" to="/admin/members">Manage Members</router-link>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="six columns">
                    <div class="card">
                        <p class="card__title"><span>Gyms</span>View Gyms / Locations</p>
                        <span class="card__value" style="margin-bottom: 1rem; height: 40px;"><i class="material-icons" style="font-size: 2rem;">shopping_cart</i></span>
                        <router-link class="button" to="/admin/gyms">View Locations</router-link>
                    </div>
                </div>

                <div class="six columns">
                    <div class="card">
                        <p class="card__title"><span>Studio Bookings</span>View Reservations</p>
                        <span class="card__value" style="margin-bottom: 1rem; height: 40px;"><i class="material-icons" style="font-size: 2rem;">event</i></span>
                        <router-link class="button" to="/admin/studio/bookings">View Reservations</router-link>
                    </div>
                </div>
            </div>
        </section>

        <!-- <section class="page-content">
            <div class="row">
                <div class="six-lg columns">
                    <section class="tab tab--none tab--bottom">
                        <ul>
                            <li class="active"><a>On Demand</a></li>
                        </ul>
                    </section>

                    <div class="row row--equal">
                        <div class="six columns">
                            <div class="card">
                                <p class="card__title"><span>{{ currentMonth }}</span>Total Views</p>
                                <span class="card__value">{{ this.ondemand.totalViews }}</span>
                                <i class="card__info fas fa-info" @click="openInfo('New Sign Ups', 'You have had ' + stats.newSignUps + ' new members sign up this month')"></i>
                            </div>
                        </div>

                        <div class="six columns">
                            <div class="card">
                                <p class="card__title"><span>{{ currentMonth }}</span>Top Video</p>
                                <span class="card__value card__value--green" v-if="this.ondemand.topVideo !== null">{{ this.ondemand.topVideo.class.name }}</span>
                                <span class="card__value card__value--green" v-else>N/A</span>
                                <i class="card__info fas fa-info" @click="openInfo('New Sign Ups', 'You have had ' + stats.newSignUps + ' new members sign up this month')"></i>
                            </div>
                        </div>
                    </div>

                    <div class="graph">
                        <p class="graph__title">30 Day Trend</p>
                        <span class="graph__value graph__value--green">{{ this.onDemandTrendGraphSum }} Views</span>
                        <line-graph :data="onDemandTrendGraph" v-if="onDemandTrendGraph !== null" />
                    </div>
                </div>

                <div class="six-lg columns">
                    <section class="tab tab--none tab--bottom">
                        <ul>
                            <li class="active"><a>Memberships</a></li>
                        </ul>
                    </section>

                    <div class="row row--equal">
                        <div class="six columns">
                            <div class="card">
                                <p class="card__title"><span>{{ currentMonth }}</span>New Members</p>
                                <span class="card__value">{{ this.live.totalBookings }}</span>
                                <i class="card__info fas fa-info" @click="openInfo('New Members', 'You have had ' + stats.newSignUps + ' new members sign up this month')"></i>
                            </div>
                        </div>

                        <div class="six columns">
                            <div class="card">
                                <p class="card__title"><span>{{ currentMonth }}</span>Renewals</p>
                                <span class="card__value card__value--green">{{ this.live.uniqueBookings }}</span>
                                <i class="card__info fas fa-info" @click="openInfo('Renewals', 'You have had ' + stats.newSignUps + ' members renew their memberships')"></i>
                            </div>
                        </div>
                    </div>

                    <div class="graph">
                        <p class="graph__title">30 Day Trend</p>
                        <span class="graph__value">{{ this.liveTrendGraphSum }} Renewals and Purchases</span>
                        <line-graph :data="liveTrendGraph" v-if="liveTrendGraph !== null" />
                    </div>
                </div>

                <div class="six-lg columns">
                    <section class="tab tab--none tab--bottom">
                        <ul>
                            <li class="active"><a>Live Classes</a></li>
                        </ul>
                    </section>

                    <div class="row row--equal">
                        <div class="six columns">
                            <div class="card">
                                <p class="card__title"><span>{{ currentMonth }}</span>Total Bookings</p>
                                <span class="card__value">{{ this.live.totalBookings }}</span>
                                <i class="card__info fas fa-info" @click="openInfo('New Sign Ups', 'You have had ' + stats.newSignUps + ' new members sign up this month')"></i>
                            </div>
                        </div>

                        <div class="six columns">
                            <div class="card">
                                <p class="card__title"><span>{{ currentMonth }}</span>Unique Bookings</p>
                                <span class="card__value card__value--green">{{ this.live.uniqueBookings }}</span>
                                <i class="card__info fas fa-info" @click="openInfo('New Sign Ups', 'You have had ' + stats.newSignUps + ' new members sign up this month')"></i>
                            </div>
                        </div>
                    </div>

                    <div class="graph">
                        <p class="graph__title">30 Day Trend</p>
                        <span class="graph__value">{{ this.liveTrendGraphSum }} Bookings</span>
                        <line-graph :data="liveTrendGraph" v-if="liveTrendGraph !== null" />
                    </div>
                </div>
            </div>
        </section> -->
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import LineGraph from './../graphs/LineGraph.vue';

export default {
    components: {
        LineGraph
    },

    data() {
        return {
            currentMonth: moment().format('MMMM') + '\'s',

            ondemand: {
                totalViews: 0,
                topVideo: {
                    class: {}
                }
            },

            onDemandTrendGraph: null,
            onDemandTrendGraphSum: 0,

            live: {
                totalBookings: 0,
                uniqueBookings: 0
            },

            liveTrendGraph: null,
            liveTrendGraphSum: 0
        }
    },

    mounted() {
        this.loadOnDemandStats();
        this.loadMembershipStats();
        // this.loadLiveStats();
    },

    methods: {
        loadOnDemandStats() {
            axios.get('/api/admin/dashboard/main/on-demand').then(response => {
                this.ondemand = response.data;

                this.onDemandTrendGraph = {
                    labels: response.data.trendGraph.legend,
                    datasets: [
                        {
                            label: 'Views',
                            borderColor: '#4DC398',
                            backgroundColor: 'rgba(77,195,152,0.05)',
                            data: response.data.trendGraph.data,
                        }
                    ]
                }

                this.onDemandTrendGraphSum = response.data.totalViews;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            });
        },

        loadLiveStats() {
            axios.get('/api/admin/dashboard/main/live').then(response => {
                this.live = response.data;

                this.liveTrendGraph = {
                    labels: response.data.trendGraph.legend,
                    datasets: [
                        {
                            label: 'Views',
                            borderColor: '#6517FF',
                            backgroundColor: 'rgba(101,23,255, 0.05)',
                            data: response.data.trendGraph.data,
                        }
                    ]
                }

                this.liveTrendGraphSum = response.data.totalBookings;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            });
        },

        loadMembershipStats() {
            axios.get('/api/admin/dashboard/main/live').then(response => {
                this.live = response.data;

                this.liveTrendGraph = {
                    labels: response.data.trendGraph.legend,
                    datasets: [
                        {
                            label: 'Renewals & New Purchases',
                            borderColor: '#6517FF',
                            backgroundColor: 'rgba(101,23,255, 0.05)',
                            data: response.data.trendGraph.data,
                        }
                    ]
                }

                this.liveTrendGraphSum = response.data.totalBookings;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            });
        }
    }
}
</script>
