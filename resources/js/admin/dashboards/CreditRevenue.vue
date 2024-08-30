<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--dashboard">
                            <i class="material-icons">paid</i>
                        </div>
                        Dashboards
                    </h1>

                    <h2 class="page-header__sub">Revenue</h2>
                </div>
            </div>
        </section>

        <section class="tab">
            <ul>
                <li><router-link class="tab__link" to="/admin/dashboard/revenue/memberships">Memberships</router-link></li>
                <li class="active"><span class="tab__link">Credit Packs</span></li>
            </ul>
        </section>

        <section class="page-content">
            <div class="row row--equal">
                <div class="six four-xl columns">
                    <div class="card">
                        <p class="card__title"><span>Credit Pack Revenue</span>Today</p>
                        <span class="card__value">£{{ periodRevenue.today }}</span>
                        <i class="card__info fas fa-info" @click="openInfo('Todays Credit Pack Revenue', 'This is an estimation based on the amount of sales you have made. To view a complete list of transactions, in-going and out-going payments please refer to your Stripe portal.')"></i>
                    </div>
                </div>
                <div class="six four-xl columns">
                    <div class="card">
                        <p class="card__title"><span>Credit Pack Revenue</span>This Week</p>
                        <span class="card__value card__value--green">£{{ periodRevenue.week }}</span>
                        <i class="card__info fas fa-info" @click="openInfo('This Weeks Credit Pack Revenue', 'This is an estimation based on the amount of sales you have made. To view a complete list of transactions, in-going and out-going payments please refer to your Stripe portal.')"></i>
                    </div>
                </div>
                <div class="six four-xl columns">
                    <div class="card">
                        <p class="card__title"><span>Credit Pack Revenue</span>This Month</p>
                        <span class="card__value">£{{ periodRevenue.month }}</span>
                        <i class="card__info fas fa-info" @click="openInfo('This Months Credit Pack Revenue', 'This is an estimation based on the amount of sales you have made. To view a complete list of transactions, in-going and out-going payments please refer to your Stripe portal.')"></i>
                    </div>
                </div>
                <!-- <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>Credit Pack Revenue</span>This Year</p>
                        <span class="card__value  card__value--green">£{{ periodRevenue.year }}</span>
                        <i class="card__info fas fa-info" @click="openInfo('This Years Membership Revenue', 'This is an estimation based on the amount of sales you have made. To view a complete list of transactions, in-going and out-going payments please refer to your Stripe portal.')"></i>
                    </div>
                </div> -->
            </div>

            <div class="row row--equal">
                <div class="twelve columns">
                    <div class="graph">
                        <p class="graph__title">2 Week Sales Trend</p>
                        <span class="graph__value">£{{ this.thirtyDayTrendGraphSum }}</span>
                        <div class="graph__container">
                            <line-graph :data="thirtyDayTrendGraph" v-if="!thirtyDayTrendGraphLoading" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="eight columns">
                    <div class="table-list">
                        <div class="table-list__header">
                            <h3>This Months Credit Pack Performance</h3>
                        </div>

                        <div class="table-list__scroll">
                            <table class="table-list__table">
                                <thead>
                                    <tr>
                                        <th>Tier</th>
                                        <th>Sales</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(item, index) in tierPerformances" :key="index">
                                        <td><router-link :to="'/admin/memberships?tier=' + item.group">{{ item.name }}</router-link></td>
                                        <td>{{ item.count }}</td>
                                        <td>£{{ item.sum }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="four columns">
                    <pie-graph :data="genderData" is-doughnut v-if="!loadingGenderDemographic" />
                </div>
            </div>



            <section class="row page-section" style="margin-top: 2rem;">
                <div class="twelve-lg columns">
                    <div class="graph">
                        <div class="graph__header">
                            <div class="graph__header__content">
                                <p class="graph__header__title">All Credit Pack Sales</p>
                                <span class="graph__header__subtitle">View a list of all credit pack sales.</span>
                            </div>
                        </div>

                        <router-link to="/admin/orders" class="button button--outlines" style="margin: .5rem 2rem 2rem 2rem;">View All</router-link>
                    </div>
                </div>
            </section>
        </section>

        <div v-if="infoModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">{{ this.infoModalTitle }}</h2>

                <p>{{ this.infoModalMessage }}</p>

                <div class="modal-alert__buttons">
                    <button class="button button--outlines" @click="infoModal = false">Close</button>
                    <a class="button button--outline" href="https://dashboard.stripe.com/dashboard">Open Stripe</a>
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
import DataTable from '../layout/DataTable.vue';

export default {
    components: {
        LineGraph,
        TabList,
        DataTable,
        PieGraph
    },

    data() {
        return {
            infoModal: false,
            infoModalTitle: '',
            infoModalMessage: '',

            tabs: [
                { name: 'Memberships', key: 'memberships' },
                { name: 'Credit Packs', key: 'creditpacks' }
            ],
            activeTab: 'memberships',

            periodRevenue: {
                today: '£150.21',
                week: '£324.57',
                month: '£2123.94',
                year: '£15,788'
            },

            thirtyDayTrendGraphSum: 0,
            thirtyDayTrendGraph: {},
            thirtyDayTrendGraphLoading: true,

            tierPerformances: [],

            genderData: [],
            loadingGenderDemographic: true
        }
    },

    mounted() {
        this.loadStats();
        this.loadThirtyDayTrendGraph();
        this.loadTierPerformances();
    },

    methods: {
        openInfo(title, message) {
            this.infoModal = true;
            this.infoModalTitle = title;
            this.infoModalMessage = message;
        },

        loadStats() {
            axios.get('/api/admin/dashboard/revenue/credit/stats').then(response => {
                this.periodRevenue = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        /*
         * Load the number of views over the past 30 days.
         * @param {none}
         */
        loadThirtyDayTrendGraph() {
            axios.get('/api/admin/dashboard/revenue/credit/thirty-day-trend').then(response => {
                this.thirtyDayTrendGraphSum = response.data.sum;
                this.thirtyDayTrendGraph = {
                    labels: response.data.legend,
                    datasets: [
                        {
                            label: 'Sales',
                            borderColor: '#4DC398',
                            backgroundColor: 'rgba(77,195,152,0.05)',
                            data: response.data.data,
                        }
                    ]
                };

                this.thirtyDayTrendGraphLoading = false;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        loadTierPerformances() {
            axios.get('/api/admin/dashboard/revenue/credit/tier-performance').then(response => {
                this.tierPerformances = response.data.table;

                this.genderData = {
                    labels: response.data.graph.legend,
                    datasets: [{
                        data: response.data.graph.data,
                        backgroundColor: ['#6517FF', '#3392D0', '#4DC398']
                    }]
                }

                this.loadingGenderDemographic = false;
            })
        },
    }
}
</script>
