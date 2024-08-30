<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">query_stats</i>
                        </div>
                        Dashboards
                    </h1>

                    <h2 class="page-header__sub">User Stats</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <div class="row row--equal">
                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>New Sign Ups</p>
                        <span class="card__value">{{ this.stats.newSignUps }}</span>
                        <i class="card__info fas fa-info" @click="openInfo('New Sign Ups', 'You have had ' + stats.newSignUps + ' new members sign up this month')"></i>
                    </div>
                </div>

                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>{{ currentMonth }}</span>In-Active Users</p>
                        <span class="card__value card__value--green">{{ this.stats.inactive }}</span>
                        <i class="card__info fas fa-info" @click="openInfo('In-Active Users', '')"></i>
                    </div>
                </div>

                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>Total Member</span>Average Age</p>
                        <span class="card__value">{{ this.stats.averageAge }}</span>
                        <i class="card__info fas fa-info" @click="openInfo('Average Age', 'The average age of all of your members is ' + stats.averageAge)"></i>
                    </div>
                </div>

                <div class="six three-xl columns">
                    <div class="card">
                        <p class="card__title"><span>Total Member</span>M/F Gender Ratio</p>
                        <span class="card__value card__value--green">{{ this.stats.genderRatio }}</span>
                        <i class="card__info fas fa-info" @click="openInfo('Male/Female Gender Ratio', 'The Male to Female gender ratio of all of your members is ' + stats.genderRatio)"></i>
                    </div>
                </div>
            </div>

            <div class="row row--equal">
                <!-- <div class="twelve columns twelve-xl">
                    <div class="graph">
                        <p class="graph__title">New Accounts</p>
                        <span class="graph__value">181 <small>avg / per month</small></span>
                        <line-graph :data="dailyUsageData" />
                    </div>
                </div> -->

                <div class="twelve columns twelve-xl">
                    <div class="table-list">
                        <div class="table-list__header">
                            <h3>More data and graphs will become available as your platforms activity increases.</h3>
                        </div>
                    </div>
                </div>
            </div>
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

export default {
    components: {
        LineGraph
    },

    data() {
        return {
            currentMonth: moment().format('MMMM') + '\'s',

            dailyUsageData: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'New Accounts Created',
                        borderColor: '#6517FF',
                        backgroundColor: 'rgba(101,23,255, 0.05)',
                        data: [200, 189, 230, 290, 150, 185, 175, 201, 206, 225, 184, 199],
                    }
                ]
            },
            userActivityData: {
                labels: ['06-08', '08-10', '10-12', '12-14', '14-16', '16-18', '18-20', '20-22', '22-00', '00-02', '02-04', '04-06'],
                datasets: [
                    {
                        label: 'Users Active',
                        borderColor: '#4DC398',
                        backgroundColor: 'rgba(77,195,152,0.05)',
                        data: [200, 189, 230, 290, 150, 185, 175, 201, 206, 225, 184, 199],
                    }
                ]
            },

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
