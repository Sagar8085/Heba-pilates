<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">donut_large</i>
                        </div>
                        Dashboards
                    </h1>

                    <h2 class="page-header__sub">Penetration</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <div class="row">
                <div v-for="(item, index) in liveCategories" :key="index" class="six-sm three-xl columns">
                    <router-link :to="'/admin/dashboard/penetration/live/' + item.id" class="percentage-card">
                        <p class="percentage-card__title">{{ item.name }}</p>
                        <p class="percentage-card__value">{{ item.penetration_rate }}%<span>Penetration</span></p>

                        <div class="percentage-card__circle">
                            <vue-easy-pie-chart
                                :percent="item.penetration_rate"
                                :size="150"
                                :lineWidth="10"
                                :scaleLength="0"
                                :trackColor="'#C2CBD1'"
                                :barColor="randomColour()"
                                :lineCap="'square'">

                                <i class="fas fa-dumbbell"></i>
                            </vue-easy-pie-chart>
                        </div>

                        <p class="percentage-card__info">Penetration: {{ item.penetration_rate }}% of your {{ totalMembers }} members booked {{ item.name }} atleast once this month.</p>
                    </router-link>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import VueEasyPieChart from 'vue-easy-pie-chart'
import 'vue-easy-pie-chart/dist/vue-easy-pie-chart.css'

export default {
    components: { VueEasyPieChart },

    data() {
        return {
            classes: [],
            liveCategories: [],
            totalMembers: 0
        }
    },

    mounted() {
        this.fetchLivePenetration();
    },

    methods: {
        /*
         * Fetch all live classes.
         * @param {none}
         */
        fetchLivePenetration() {
            axios.get('/api/admin/dashboard/penetration/live').then(response => {
                this.liveCategories = response.data.classes;
                this.totalMembers = response.data.total_members;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
        },

        randomColour() {
            const months = ["#6517FF", "#FCBE00", "#FA7C42", "#EC7171", "#A5A4FF", "#EA51DE", "#4DC398"];
            const random = Math.floor(Math.random() * months.length);
            return months[random];
        },

        changeTab(tab) {
            this.tab = tab;
        }
    }
}
</script>
