<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">assignment_ind</i>
                        </div>
                        {{ agent.name }}
                    </h1>

                    <h2 class="page-header__sub">Sales Agent Profile</h2>
                </div>
            </div>
        </section>

        <div class="lead-management agent-profile">
            <div class="wrapper">
                <div class="row">
                    <div class="eight columns">

                        <div class="target-boxes target-boxes--four">
                            <div class="target-boxes__col">
                                <div class="target-box">
                                    <div class="target-box__inner">
                                        <p class="target-box__title">Calls</p>

                                        <div class="target-box__edit">
                                            <button @click="showSetTargetModal('Calls')" v-if="this.authUser.privileges.includes('lead-admin')">Set target</button>
                                        </div>

                                        <div class="target-box__bar" v-tooltip="Math.round((this.agentPerformance.calls_made / this.agentPerformance.calls_target) * 100) + '% Complete'">
                                            <span :style="'width: ' + (this.agentPerformance.calls_made / this.agentPerformance.calls_target) * 100 + '%'"></span>
                                        </div>

                                        <div class="row">
                                            <div class="four columns">
                                                <label class="target-box__label">Total</label>
                                                <span class="target-box__value">{{this.agentPerformance.calls_made}}</span>
                                            </div>

                                            <div class="four columns center">
                                                <label class="target-box__label">Target</label>
                                                <span class="target-box__value">{{this.agentPerformance.calls_target}}</span>
                                            </div>

                                            <div class="four columns right">
                                                <label class="target-box__label">Left</label>
                                                <span class="target-box__value">{{this.agentPerformance.calls_remaining > 0 ? this.agentPerformance.calls_remaining : 0}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="target-boxes__col">
                                <div class="target-box">
                                    <div class="target-box__inner">
                                        <p class="target-box__title">Appointments</p>

                                        <div class="target-box__edit">
                                            <button @click="showSetTargetModal('Appointments')" v-if="this.authUser.privileges.includes('lead-admin')">Set target</button>
                                        </div>

                                        <div class="target-box__bar" v-tooltip="Math.round((this.agentPerformance.appointments_made / this.agentPerformance.appointments_target) * 100) + '% Complete'">
                                            <span :style="'width: ' + (this.agentPerformance.appointments_made / this.agentPerformance.appointments_target) * 100 + '%'"></span>
                                        </div>

                                        <div class="row">
                                            <div class="four columns">
                                                <label class="target-box__label">Total</label>
                                                <span class="target-box__value">{{this.agentPerformance.appointments_made}}</span>
                                            </div>

                                            <div class="four columns center">
                                                <label class="target-box__label">Target</label>
                                                <span class="target-box__value">{{this.agentPerformance.appointments_target}}</span>
                                            </div>

                                            <div class="four columns right">
                                                <label class="target-box__label">Left</label>
                                                <span class="target-box__value">{{this.agentPerformance.appointments_remaining > 0 ? this.agentPerformance.appointments_remaining : 0}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="target-boxes__col">
                                <div class="target-box">
                                    <div class="target-box__inner">
                                        <p class="target-box__title">Signups</p>

                                        <div class="target-box__edit">
                                            <button @click="showSetTargetModal('Sign Ups')" v-if="this.authUser.privileges.includes('lead-admin')">Set target</button>
                                        </div>

                                        <div class="target-box__bar" v-tooltip="Math.round((this.agentPerformance.signups_made / this.agentPerformance.signups_target) * 100) + '% Complete'">
                                            <span :style="'width: ' + (this.agentPerformance.signups_made / this.agentPerformance.signups_target) * 100 + '%'"></span>
                                        </div>

                                        <div class="row">
                                            <div class="four columns">
                                                <label class="target-box__label">Total</label>
                                                <span class="target-box__value">{{this.agentPerformance.signups_made}}</span>
                                            </div>

                                            <div class="four columns center">
                                                <label class="target-box__label">Target</label>
                                                <span class="target-box__value">{{this.agentPerformance.signups_target}}</span>
                                            </div>

                                            <div class="four columns right">
                                                <label class="target-box__label">Left</label>
                                                <span class="target-box__value">{{this.agentPerformance.signups_remaining > 0 ? this.agentPerformance.signups_remaining : 0}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="target-boxes__col">
                                <div class="target-box">
                                    <div class="target-box__inner">
                                        <p class="target-box__title">Close Ratio</p>

                                        <div class="target-box__edit">
                                            <button @click="showSetTargetModal('Close Ratio')" v-if="this.authUser.privileges.includes('lead-admin')">Set target</button>
                                        </div>

                                        <div class="target-box__bar" v-tooltip="Math.round((this.agentPerformance.close_ratio_current / this.agentPerformance.close_ratio_target) * 100) + '% Complete'">
                                            <span :style="'width: ' + (this.agentPerformance.close_ratio_current / this.agentPerformance.close_ratio_target) * 100 + '%'"></span>
                                        </div>

                                        <div class="row">
                                            <div class="four columns">
                                                <label class="target-box__label">Total</label>
                                                <span class="target-box__value">{{this.agentPerformance.close_ratio_current}}%</span>
                                            </div>

                                            <div class="four columns center">
                                                <label class="target-box__label">Target</label>
                                                <span class="target-box__value">{{this.agentPerformance.close_ratio_target}}%</span>
                                            </div>

                                            <div class="four columns right">
                                                <label class="target-box__label">Left</label>
                                                <span class="target-box__value">{{this.agentPerformance.close_ratio_remaining > 0 ? this.agentPerformance.close_ratio_remaining : 0}}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="four columns">
                        <div class="lead-management__dashboard__col" style="margin: 0;">
                            <div class="lead-management__dashboard__col__box achievement-box empty" v-if="!this.recent_achievement">
                                <img src="/images/icons/img-achievments-emptyState.png" />
                                <label class="subtitle" style="margin-top: 5px; margin-bottom: 0;">You haven't unlocked any achievements yet.</label>
                                <label class="subtitle" style="margin-top: 5px; margin-bottom: 0;">When you do they will appear here.</label>
                            </div>

                            <div class="lead-management__dashboard__col__box achievement-box" v-else>
                                <img :src="this.recent_achievement.details.image_path" />
                                <div style="padding: 10px;">
                                    <label class="title" style="display: block; margin-bottom: 5px;">{{this.recent_achievement.details.name}}</label>
                                    <label class="subtitle">{{this.recent_achievement.details.description}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="leadstab">
                    <div class="wrapper">
                        <ul>
                            <li :class="this.tab === 'leads' ? 'active' : ''"><a @click="tab = 'leads'">Leads</a></li>
                            <li :class="this.tab === 'signups' ? 'active' : ''"><a @click="tab = 'signups'">Recent sign ups</a></li>
                            <li :class="this.tab === 'appointments' ? 'active' : ''"><a @click="tab = 'appointments'">Presentations and Appointments</a></li>
                        </ul>
                    </div>
                </section>

                <div class="wrapper">
                    <div class="list-wrap">
                        <section v-if="tab === 'leads'">
                            <pagination class="pagination--top" @change_page="change_page" :pagination="this.pagination" :loading="this.loading"></pagination>

                            <table class="list lead-management__dashboard__list" v-if="leads.length > 0">
                                <thead>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Calls Made</th>
                                    <th>Appointment</th>
                                    <th>Fitness Goal</th>
                                    <th>Lead Source</th>
                                </thead>
                                <tbody>
                                    <tr v-for="(lead, index) in leads" :key="index">
                                        <td class="fixed">
                                            <router-link :to="'/admin/leads/' + lead.id">
                                                <img :src="lead.image_url"> {{ lead.full_name }}
                                            </router-link>
                                        </td>
                                        <td class="fixed">{{ lead.email }}</td>
                                        <td>{{ lead.phone_number }}</td>
                                        <td class="lead-management__dashboard__list__call-cell">
                                            <div>
                                                <div v-for="i in 3" :class="lead.calls_made >= i ? 'number-indicator active' : 'number-indicator'"></div>
                                            </div>
                                        </td>
                                        <td>{{ lead.appointment }}</td>
                                        <td>{{ lead.fitness_goal_human }}</td>
                                        <td>{{ lead.source }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <section class="forbidden" v-else>
                                <img src="/images/member-portal/empty.svg">
                                <h2>No Leads</h2>
                                <p>Uh oh! It looks like this agent doesn't have any open leads assigned.</p>
                            </section>
                        </section>

                        <section v-if="tab === 'signups'">
                            <table class="list lead-management__dashboard__list" v-if="recentSignUps.length > 0">
                                <thead>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Calls Made</th>
                                    <th>Appointment</th>
                                    <th>Interested</th>
                                    <th>Fitness Goal</th>
                                    <th>Lead Source</th>
                                    <th>Temperature</th>
                                </thead>
                                <tbody>
                                    <tr v-for="(lead, index) in recentSignUps" :key="index">
                                        <td class="fixed">
                                            <router-link :to="'/admin/members/' + lead.user_id + '#lead'">
                                                <img :src="lead.image_url"> {{ lead.full_name }}
                                            </router-link>
                                        </td>
                                        <td class="fixed">{{ lead.email }}</td>
                                        <td>{{ lead.phone_number }}</td>
                                        <td class="lead-management__dashboard__list__call-cell">
                                            <div>
                                                <div v-for="i in 3" :class="lead.calls_made >= i ? 'number-indicator active' : 'number-indicator'"></div>
                                            </div>
                                        </td>
                                        <td>{{ lead.appointment }}</td>
                                        <td>{{ lead.interested === 1 ? 'Yes' : 'No' }}</td>
                                        <td>{{ lead.fitness_goal_human }}</td>
                                        <td>{{ lead.source }}</td>
                                        <td>
                                            <div class="lead-management__dashboard__list__temperature-cell">{{lead.temperature}}</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <section class="forbidden" v-else>
                                <img src="/images/member-portal/empty.svg">
                                <h2>No Recent Sign Ups</h2>
                                <p>Uh oh! It looks like this agent hasn't made any conversions yet.</p>
                            </section>
                        </section>
                    </div>
                </div>

                <section v-if="tab === 'appointments'" class="wrapper">
                    <div class="chart-container__item">
                        <div class="chart-container__item__half-size-center__header">
                            <h1>PRESENTATIONS</h1>
                        </div>
                        <div class="chart-container__item__half-size-center__header">

                            <h1>APPOINTMENTS</h1>
                        </div>

                        <div class="chart-container__item__half-size-center">
                            <div>
                                <pie-graph :isDoughnut="true" v-if="!this.loading" :data="this.presentationsChartData"></pie-graph>
                            </div>
                        </div>

                        <div class="chart-container__item__half-size-center">
                            <div>
                                <bar-graph :stacked="true" v-if="!this.loading" :data="this.appointmentsChartData"></bar-graph>
                            </div>
                        </div>
                    </div>
                </section>


                <!-- dgsd -->
        </div>

        <set-target-modal :name="modalName" v-on:cancel="displaySetTargetModal = false" v-on:complete="displaySetTargetModal = false; load()" :class="displaySetTargetModal ? 'modal modal--active' : 'modal'" />
    </div>
</template>
<script>
import axios from 'axios';
import SetTargetModal from './SetTargetModal.vue';
import BarGraph from '../graphs/BarGraph.vue';
import PieGraph from '../graphs/PieGraph.vue';

export default {
    props: {
        authUser: Object,
        chartData: Object
    },

    components: {
        SetTargetModal,
        BarGraph,
        PieGraph
    },


    data() {
        return {
            agent: {},
            loading: false,

            calls_made: 0,
            target_calls: 20,
            remaining_calls: null,
            recentSignUps: [],
            recent_achievement: '',

            appointments_made: 0,
            target_appointments: 5,
            remaining_appointments: null,
            displaySetTargetModal: false,

            modalName: '',

            signups_made: 0,
            target_signups: 3,
            remaining_signups: null,

            agentPerformance: {},
            leads: [],
            pagination: {},

            tab: 'leads',
            newSignUpsChartData: {
                labels: ['11/09/2020', '12/09/2020', '13/09/2020', '14/09/2020', '15/09/2020', '16/09/2020', '17/09/2020', '18/09/2020'],
                datasets: [
                    {
                        label: 'New sign ups',
                        backgroundColor: '#FD7501',
                        data: [10, 5, 2, 18, 8, 4, 15, 1],
                    }
                ]
            },
            presentationsChartData: {
                labels: ['Website', 'Walk-In'],
                datasets: [
                    {
                        label: 'Lead Sources',
                        backgroundColor: ['#FEB87B', '#C6AAFC'],
                        data: [15, 10],
                        pointStyle: 'rectRot',
                        weight: 1,
                        borderWidth: 1
                    }
                ]
            },
            appointmentsChartData: {
                labels: ['11/09/2020', '12/09/2020', '13/09/2020', '14/09/2020', '15/09/2020', '16/09/2020'],
                datasets: [
                    {
                        label: 'Lead Sources',
                        backgroundColor: ['#B0E679', '#B0E679', '#B0E679', '#B0E679', '#B0E679', '#B0E679'],
                        data: [15, 10, 20, 30, 50, 20,],
                    },
                    {
                        label: 'No Shows',
                        backgroundColor: ['#EC7171', '#EC7171', '#EC7171', '#EC7171', '#EC7171', '#EC7171'],
                        data: [15, 10, 15, 20, 50, 10,],
                    }
                ]
            },
        }
    },

    mounted() {
        /*
         * Only leads admins are allowed to view agent profiles.
         */
        // if (this.authUser.privileges.includes('lead-admin')) {
            this.load();
        // } else {
            // this.$router.push('/admin/leads');
        // }
    },

    methods: {
        load() {
            this.loadAdmin();
            this.fetchPerformance();
            this.fetchLeads();
            this.fetchSignUps();
        },
        /*
         * Load sales agent profile.
         * @param {none}
         */
        loadAdmin() {
            axios.get('/api/admin/leads/manage/agents/' + this.$route.params.id)
            .then(response => {
                this.agent = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        showSetTargetModal(name) {
            this.modalName = name;
            console.log(name);
            this.displaySetTargetModal = true;
        },

        hideModals() {
            this.displayAddLeadModal = false;
        },

        /*
         * Load sales agent performance stats.
         * @param {none}
         */
        fetchPerformance() {
            axios.get('/api/admin/leads/manage/agents/' + this.$route.params.id + '/performance').then(response => {
                this.agentPerformance = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        },

        /*
         * Fetch list of all leads assigned to this agent.
         * @param {none}
         */
        fetchLeads(page) {
            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/leads/manage/agents/' + this.$route.params.id + '/leads?page=' + page).then(response => {
                this.leads = response.data.data;
                this.pagination = response.data;
                this.loading = false;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        },

        /*
         * Called from the pagination component when user is attempting to go forward or back a page.
         * @param {type} string
         */
        change_page(type) {
            this.loading = true;

            if (type === 'previous') {
                this.fetchLeads((this.pagination.current_page - 1));
            } else {
                this.fetchLeads((this.pagination.current_page + 1));
            }
        },

        /*
         * Fetch list of all leads this agent has converted into sales.
         * @param {none}
         */
        fetchSignUps() {
            axios.get('/api/admin/leads/manage/agents/' + this.$route.params.id + '/conversions').then(response => {
                this.recentSignUps = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        }
    }
}
</script>
