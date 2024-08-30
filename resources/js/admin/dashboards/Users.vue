<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">query_stats</i>
                        </div>
                        Guest Statistics
                    </h1>

                    <h2 class="page-header__sub">Dashboard</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <ReportingFilters @filter="filter" />

            <div class="row row--equal">
                <div class="twelve columns">
                    <ComparisonLineGraph
                        title="Total Live Guests"
                        subtitle="Active and Idle Guests"
                        large-title
                        :graphData="dailyUsageData"
                        :graphHeight="300"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>
            </div>

            <h3 class="page__title page__title--top">Guest Statistics</h3>

            <div class="row row--equal">
                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="New Signups"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Prospective Guests"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Active Guests"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Idle Guests"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Lapsed Guests"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Unsubscribers (disengaged)"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Subscriber:Guests Ratio"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>
            </div>

            <h3 class="page__title page__title--top">Session Statistics</h3>

            <div class="row row--equal">
                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Sessions Served"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Average Session Frequency"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Unique Visitors"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Number of Bookings"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Number of Cancellations"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonPieChart
                        title="Session Delivery"
                        :graphData="sessionDeliveryData"
                        :graphValueData="pieGraphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>
            </div>

            <h3 class="page__title page__title--top">Demographics</h3>

            <div class="row row--equal">
                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Average Age of All Guests"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonLineGraph
                        title="Average Age of Live Guests"
                        :graphData="dailyUsageData"
                        :graphValueData="graphValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>

                <div class="columns six-sm four-xl">
                    <ComparisonPieChart
                        title="Gender Ratio"
                        :graphData="genderRatioData"
                        :graphValueData="genderRatioValueData"
                        @info="openInfo('Total Live Guests', 'Total live guests shows the current number of active customers.')"
                        />
                </div>
            </div>
        </section>

        <div v-if="infoModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">{{ this.infoModalTitle }}</h2>

                <p>{{ this.infoModalMessage }}</p>

                <div class="modal-alert__buttons">
                    <button class="button" @click="infoModal = false">Okay</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import LineGraph from './../graphs/LineGraph.vue';
import ReportingFilters from './components/ReportingFilters.vue';
import ComparisonLineGraph from './components/ComparisonLineGraph.vue';
import ComparisonPieChart from './components/ComparisonPieChart.vue';

export default {
    components: {
        LineGraph,
        ReportingFilters,
        ComparisonLineGraph,
        ComparisonPieChart
    },

    data() {
        return {
            graphValueData: [
                { value: 299, valueDiffPercentage: '+112%', valueDiffType: 'positive' },
                { value: 123, valueDiffPercentage: '-112%', valueDiffType: 'negative' }
            ],

            pieGraphValueData: [
                { value: 'S 87%/C 13%' },
                { value: 'S 82%/C 18%' }
            ],

            dailyUsageData: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'New Accounts Created',
                        data: [200, 189, 230, 290, 150, 185, 175, 201, 206, 225, 184, 199],
                    },
                    {
                        label: 'New Accounts Created',
                        data: [189, 230, 290, 150, 185, 175, 201, 206, 225, 184, 199, 222],
                    }
                ]
            },

            sessionDeliveryData: {
                labels: ['Sessions Served', 'Sessions Cancelled'],
                datasets: [
                    {
                        data: [87, 13],
                        backgroundColor: ['#17B5C8', '#eb4a4a']
                    },

                    {
                        data: [82, 18],
                        backgroundColor: ['#17B5C8', '#eb4a4a']
                    }
                ]
            },

            genderRatioData: {
                labels: ['Female', 'Male', 'Unspecified'],
                datasets: [
                    {
                        data: [85, 15, 5],
                        backgroundColor: ['#d36ea8', '#17B5C8', '#ffb95b']
                    },
                    {
                        data: [87, 12, 9],
                        backgroundColor: ['#d36ea8', '#17B5C8', '#ffb95b']
                    }
                ]
            },

            genderRatioValueData: [
                { value: 'm 15%/f 85%/un 5%' },
                { value: 'm 12%/f 87%/un 9%' }
            ],

            infoModal: false,
            infoModalTitle: '',
            infoModalMessage: '',

            stats: {}
        }
    },

    mounted() {
        this.loadStats();
    },

    methods: {
        filter (filters) {
            // Call API
        },

        loadStats() {
            axios.get('/api/admin/dashboard/users/stats').then(response => {
                this.stats = response.data;
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
