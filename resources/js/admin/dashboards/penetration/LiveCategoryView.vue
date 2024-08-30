<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        Live Penetration | {{ this.category.name }}
                    </h1>

                    <h2 class="page-header__sub">Category Penetration Stats</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <section class="row page-section">
                <div class="twelve columns">
                    <h3 class="page__title--secondary">Popularity</h3>
                    <div class="graph">
                        <div class="graph__header">
                            <div class="graph__header__content">
                                <h4 class="graph__header__title">{{ this.category.name }}</h4>
                                <span class="graph__header__subtitle">Bookings for live classes within this category over the last 30 days</span>
                            </div>
                        </div>
                        <line-graph :data="popularityData" v-if="!popularityDataLoading" />
                    </div>
                </div>
            </section>

            <h3 class="page__title--secondary">Demographics</h3>
            <section class="row page-section">
                <div class="six-lg columns">
                    <div class="graph">
                        <div class="graph__header">
                            <div class="graph__header__content">
                                <p class="graph__header__title">Age</p>
                                <span class="graph__header__subtitle">Members who have engaged with classes assigned to this category</span>
                            </div>
                            <div class="graph__header__info">
                                <i class="fas fa-info-circle" />
                            </div>
                        </div>
                        <percentage-bar-graph :data="ageData" v-if="!loadingAgeDemographic" />
                    </div>
                </div>
                <div class="six-lg columns">
                    <div class="graph">
                        <div class="graph__header">
                            <div class="graph__header__content">
                                <p class="graph__header__title">Gender</p>
                                <span class="graph__header__subtitle">Members who have engaged with classes assigned to this category</span>
                            </div>
                            <div class="graph__header__info">
                                <i class="fas fa-info-circle" />
                            </div>
                        </div>
                        <pie-graph :data="genderData" is-doughnut is-semi-circle v-if="!loadingGenderDemographic" />
                    </div>
                </div>
            </section>

            <h3 class="page__title--secondary">Member Penetration</h3>
            <section class="row page-section">
                <div class="twelve-lg columns">
                    <div class="graph">
                        <div class="graph__header">
                            <div class="graph__header__content">
                                <p class="graph__header__title">Member Penetration</p>
                                <span class="graph__header__subtitle">Members who have booked a class assigned to this category</span>
                            </div>
                            <div class="graph__header__info">
                                <i class="fas fa-info-circle" />
                            </div>
                        </div>

                        <router-link :to="'/admin/dashboard/penetration/live/' + this.$route.params.category_id + '/members'" class="button button--outlines" style="margin: .5rem 2rem 2rem 2rem;">View Member List</router-link>
                    </div>
                </div>
            </section>

            <h3 class="page__title--secondary">Top Performing Classes</h3>
        </section>

        <div class="list-wrap">
            <table class="list list--no-top list--new list--side list--with-hover">
                <thead>
                    <th>Class Name</th>
                    <th>Bookings</th>
                </thead>
                <tbody>
                    <tr v-for="item in this.topClasses">
                        <td><router-link :to="'/admin/live-classes/' + item.liveclass_id">{{ item.class.datetime_human }}</router-link></td>
                        <td>{{item.total}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import LineGraph from './../../graphs/LineGraph.vue';
import PieGraph from '../../graphs/PieGraph.vue';
import PercentageBarGraph from '../../graphs/PercentageBarGraph.vue';

export default {
    components: {
        LineGraph,
        PieGraph,
        PercentageBarGraph
    },
    data() {
        return {
            category: {},

            popularityTimeFrame: 'today',
            popularityData: {},
            popularityDataLoading: true,

            ageData: {},
            loadingAgeDemographic: true,

            genderData: {},
            loadingGenderDemographic: true,

            topClasses: []
        }
    },

    mounted() {
        this.loadClassCategory();
        this.fetchClassList();
        this.loadViewsGraph();
        this.loadAgeDemographics();
        this.loadGenderDemographics();
    },

    methods: {
        /*
         * Load a single class.
         * @param {none}
         */
        loadClassCategory() {
            axios.get('/api/admin/live-classes/category/' + this.$route.params.category_id).then(response => {
                this.category = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        /*
         * Load the top 10 classes within this category.
         * @param {none}
         */
        fetchClassList() {
            axios.get('/api/admin/dashboard/penetration/live/' + this.$route.params.category_id + '/top-classes').then(response => {
                this.topClasses = response.data;
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
        loadViewsGraph() {
            axios.get('/api/admin/dashboard/penetration/live/' + this.$route.params.category_id + '/30-day-bookings').then(response => {
                this.popularityData = {
                    labels: response.data.legend,
                    datasets: [
                        {
                            label: 'Bookings',
                            borderColor: '#4DC398',
                            backgroundColor: 'rgba(77,195,152,0.05)',
                            data: response.data.data,
                        }
                    ]
                };

                this.popularityDataLoading = false;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        /*
         * Load age demographics.
         * @param {none}
         */
        loadAgeDemographics() {
            axios.get('/api/admin/dashboard/penetration/demographics/age?type=live-category&id=' + this.$route.params.category_id).then(response => {
                this.ages = response.data;
                this.ageData = {
                    labels: response.data.legend,
                    datasets: [
                        {
                            label: 'Members',
                            borderColor: '#3392D0',
                            backgroundColor: '#3392D0',
                            barThickness: 20,
                            data: response.data.data,
                        }
                    ]
                }
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loadingAgeDemographic = false);
        },

        /*
         * Load gender demographics.
         * @param {none}
         */
        loadGenderDemographics() {
            axios.get('/api/admin/dashboard/penetration/demographics/gender?type=live-category&id=' + this.$route.params.category_id).then(response => {
                this.genderData = {
                    labels: response.data.legend,
                    datasets: [{
                        data: response.data.data,
                        backgroundColor: ['#6517FF', '#3392D0', '#4DC398']
                    }]
                }
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loadingGenderDemographic = false);
        }
    }
}
</script>
