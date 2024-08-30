<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">supervised_user_circle</i>
                        </div>
                        Lead Management
                    </h1>

                    <h2 class="page-header__sub">Manage Leads</h2>
                </div>
                <div class="page-header__col">
                    <button @click="displayCreateModal = true" class="button">
                        Create Lead
                        <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <section class="leadstab">
            <div class="wrapper">
                <ul>
                    <li :class="this.tab === 'leads' ? 'active' : ''"><a href="#leads" @click="tab = 'leads'">Leads</a></li>
                    <li :class="this.tab === 'unassigned' ? 'active' : ''"><a href="#unassigned" @click="tab = 'unassigned'">Unassigned Leads</a></li>
                    <li :class="this.tab === 'source' ? 'active' : ''"><a href="#source" @click="tab = 'source'">Lead Sources</a></li>
                </ul>
            </div>
        </section>

        <div class="page-content lead-management">

            <div v-if="tab === 'leads'">
                <div class="wrapper wrapper--top">
                    <pagination @change_page="change_page" :pagination="this.pagination" :loading="this.loading"></pagination>
                </div>

                <section class="lead-management__dashboard">
                    <div class="wrapper">
                        <div class="list-wrap" v-if="leads.length > 0">
                            <div class="lead-management__dashboard__col">
                                <table class="list lead-management__dashboard__list">
                                    <thead>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Calls Made</th>
                                        <th>Appointment</th>
                                        <th>Fitness Goal</th>
                                        <th>Lead Source</th>
                                        <th>Temperature</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="lead in leads" v-bind:key="lead.id">
                                            <td class="fixed">
                                                <router-link :to="'/admin/leads/' + lead.id">
                                                    <img :src="lead.image_url"/> <div class="lead-management__dashboard__list__name">{{lead.full_name}}</div>
                                                </router-link>
                                            </td>
                                            <td class="fixed">{{lead.email}}</td>
                                            <td>{{lead.phone_number}}</td>
                                            <td class="lead-management__dashboard__list__call-cell">
                                                <div>
                                                    <!-- Calls -->
                                                    <div v-for="i in 3" v-bind:key="i.id" :class="lead.calls_made >= i ? 'number-indicator active' : 'number-indicator'"></div>
                                                </div>
                                            </td>
                                            <td>{{lead.appointment}}</td>
                                            <td>{{lead.fitness_goal_human}}</td>
                                            <td>{{lead.source}}</td>
                                            <td>
                                                <div class="lead-management__dashboard__list__temperature-cell">
                                                    {{lead.temperature}}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <section class="forbidden" v-else>
                            <img src="/images/member-portal/empty.svg">
                            <h2>No Leads</h2>
                            <p>We don't have any leads to show you at the moment.</p>
                        </section>
                    </div>
                </section>

                <div class="wrapper">
                    <pagination @change_page="change_page" :pagination="this.pagination" :loading="this.loading"></pagination>
                </div>
            </div>

            <div v-if="tab === 'unassigned'">
                <div class="wrapper wrapper--top">
                    <pagination @change_page="change_page" :pagination="this.pagination" :loading="this.loading"></pagination>
                </div>

                <section class="lead-management__dashboard" v-if="leads.length > 0">
                    <div class="wrapper">
                        <div class="lead-management__dashboard__col list-wrap">
                            <table class="list lead-management__dashboard__list">
                                <thead>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Calls Made</th>
                                    <th>Fitness Goal</th>
                                    <th>Lead Source</th>
                                    <th>Temperature</th>
                                    <th>Owner</th>
                                    <!-- <th><input type="checkbox"></th> -->
                                </thead>
                                <tbody>
                                    <tr v-for="lead in leads" v-bind:key="lead.id">
                                        <td class="fixed">
                                            <router-link :to="'/admin/leads/' + lead.id">
                                                <img :src="lead.image_url"/><div class="lead-management__dashboard__list__name">{{lead.full_name}}</div>
                                            </router-link>
                                        </td>
                                        <td class="fixed">{{lead.email}}</td>
                                        <td>{{lead.phone_number}}</td>
                                        <td class="lead-management__dashboard__list__call-cell">
                                            <div>
                                                <div v-for="i in 3" v-bind:key="i.id" :class="lead.calls_made >= i ? 'number-indicator active' : 'number-indicator'"></div>
                                            </div>
                                        </td>
                                        <td>{{lead.fitness_goal_human}}</td>
                                        <td>{{lead.source}}</td>
                                        <td>
                                            <div class="lead-management__dashboard__list__temperature-cell">
                                                {{lead.temperature}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="">
                                                <button class="button" @click="openReassignLeadModal(lead)">Assign</button>
                                            </div>
                                        </td>
                                        <!-- <td>
                                            <div class="lead-management__dashboard__list__assign-cell">
                                                <input type="checkbox">
                                            </div>
                                        </td> -->
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="forbidden" v-else>
                    <img src="/images/member-portal/empty.svg">
                    <h2>No Leads</h2>
                    <p>We don't have any unassigned leads to show you at the moment.</p>
                </section>

                <div class="wrapper">
                    <pagination @change_page="change_page" :pagination="this.pagination" :loading="this.loading"></pagination>
                </div>
            </div>

            <div v-if="tab === 'source'" class="wrapper">
                <div>
                    <div class="row">
                        <div style="float: right; margin-top: 2rem;">
                            <a :class="['button ', period !== 'today' ? ' button--outline' : 'button']" @click="setPeriod('today')">Today</a>
                            <a :class="['button ', period !== 'week' ? ' button--outline' : 'button']" @click="setPeriod('week')">This Week</a>
                            <a :class="['button ', period !== 'month' ? ' button--outline' : 'button']" @click="setPeriod('month')">This Month</a>
                            <a :class="['button ', period !== 'year' ? ' button--outline' : 'button']" @click="setPeriod('year')">This Year</a>
                        </div>
                    </div>

                    <section class="lead-charts">
                        <div class="lead-charts__col">
                            <label class="lead-charts__label">Lead Source Distribution</label>

                            <div class="lead-chart">
                                <div v-if="this.leadSourceDistributionHasData">
                                    <pie-graph :key="componentKey" v-if="this.leadSourceDistributionChartData !== null" :data="this.leadSourceDistributionChartData" :isDoughnut="true" :isLeadManagement="true"></pie-graph>
                                </div>

                                <div class="forbidden forbidden--no-padding" v-else>
                                    <img src="/images/member-portal/empty.svg">
                                    <h2>Not Enough Data</h2>
                                    <p>We don't have enough lead data to show you distribution of leads via source at the moment.</p>
                                </div>
                            </div>
                        </div>

                        <div class="lead-charts__col">
                            <label class="lead-charts__label">Lead KPIs</label>

                            <div class="lead-chart">
                                <div v-if="this.leadKPIHasData">
                                    <pie-graph :key="componentKey" v-if="this.leadKPIChartData !== null" :filter="this.filter" :data="this.leadKPIChartData" :isDoughnut="true" :isLeadManagement="true"></pie-graph>
                                </div>

                                <div class="forbidden forbidden--no-padding" v-else>
                                    <img src="/images/member-portal/empty.svg">
                                    <h2>Not Enough Data</h2>
                                    <p>We don't have enough lead data to show you leads KPIs at the moment.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="lead-charts">
                        <div class="lead-charts__col">
                            <label class="lead-charts__label">Converted Leads</label>

                            <div class="lead-chart">
                                <bar-graph :key="componentKey" v-if="!this.loading" :filter="this.filter" :data="this.convertedLeadsChartData" :stacked="true"></bar-graph>
                            </div>
                        </div>

                        <div class="lead-charts__col">
                            <label class="lead-charts__label">Lead Sources</label>

                            <div class="lead-chart">
                                <bar-graph :key="componentKey" v-if="this.leadSourcesChartData !== null" :filter="this.filter" :options="this.leadSourcesOptions" :data="this.leadSourcesChartData" :stacked="true"></bar-graph>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div v-if="this.displayAddLeadModal === true">
                <add-lead-modal v-on:cancel="displayAddLeadModal = false"></add-lead-modal>
            </div>

            <div class="modal-container" v-if="displayAssignEvenlyModal">
                <div class="sidebar-modal">
                    <div class="sidebar-modal__header">
                        <label class="sidebar-modal__header__title">Assign Evenly</label>
                    </div>
                    <div class="sidebar-modal__body">
                        <div class="sidebar-modal__body__text">
                            Here's how these selected leads will be distributed
                        </div>

                        <div class="sidebar-modal__body__assign-container" v-for="row in assignEvenly" v-bind:key="row.id">
                            <div>
                                <div class="avatar-with-name">
                                    <img src="/images/placeholders/taylor.jpg">
                                    <span>{{row.name}}</span>
                                </div>

                                <div class="form-element__control">
                                    <span class="form-element__control__assign">
                                        <div>
                                            <div class="form-element__control__assign__text">Current Leads</div>
                                            {{ row.current_leads }}
                                        </div>
                                    </span>
                                    <span class="form-element__control__assign">
                                        <div>
                                            <div class="form-element__control__assign__text">After Assigning</div>
                                            {{ row.total_leads }} <small>(+{{row.new_leads}})</small>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar-modal__footer">
                        <button class="button" @click="assignLeadsEvenly(true)">Assign Leads</button>
                        <button class="button button--white" @click="displayAssignEvenlyModal = false">Cancel</button>
                    </div>
                </div>
            </div>

            <div class="modal-container" v-if="displayAssignBalancedModal">
                <div class="sidebar-modal">
                    <div class="sidebar-modal__header">
                        <label class="sidebar-modal__header__title">Assign Balanced</label>
                    </div>
                    <div class="sidebar-modal__body">
                        <div class="sidebar-modal__body__text">
                            Here's how these selected leads will be distributed
                        </div>

                        <div class="sidebar-modal__body__assign-container" v-for="row in assignBalanced" v-bind:key="row.id">
                            <div>
                                <div class="avatar-with-name">
                                    <img src="/images/placeholders/taylor.jpg">
                                    <span>{{row.name}}</span>
                                </div>

                                <div class="form-element__control">
                                    <span class="form-element__control__assign">
                                        <div>
                                            <div class="form-element__control__assign__text">Current Leads</div>
                                            {{ row.current_leads }}
                                        </div>
                                    </span>
                                    <span class="form-element__control__assign">
                                        <div>
                                            <div class="form-element__control__assign__text">After Assigning</div>
                                            {{ row.total_leads }} <small>(+{{row.new_leads}})</small>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar-modal__footer">
                        <button class="button" @click="processAssignBalanced(false)">Assign Leads</button>
                        <button class="button button--white" @click="displayAssignBalancedModal = false">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <create-lead-modal v-on:cancel="displayCreateModal = false" v-on:complete="displayCreateModal = false; loadLeads()" :class="displayCreateModal ? 'modal modal--active' : 'modal'" />

        <reassign-modal v-on:cancel="loadLeads(); displayReassignLeadModal = false" v-on:complete="loadLeads(); displayReassignLeadModal = false;" :class="displayReassignLeadModal ? 'modal modal--active' : 'modal'" :full_name="reassigningLead.full_name" :image_url="reassigningLead.image_url" :lead_id="reassigningLead.id"  />
    </div>
</template>
<script>

import axios from 'axios';
import CreateLeadModal from './CreateLeadModal.vue';

import PieGraph from '../graphs/PieGraph.vue';
import BarGraph from '../graphs/BarGraph.vue';

import ReassignModal from './ReassignModal.vue';

export default {
    props: {
        authUser: Object,
    },

    components: {
        CreateLeadModal,
        PieGraph,
        BarGraph,
        ReassignModal
    },

    data() {
        return {
            displayCreateModal: false,

            period: 'today',
            componentKey: 1,
            loading: false,

            calls_made: 0,
            target_calls: 20,

            appointments_made: 0,
            target_appointments: 5,

            signups_made: 0,
            target_signups: 3,

            period: '',
            periodSet: false,

            recent_achievement: null,

            displayAddLeadModal: false,
            agent: {},

            leads: [],
            pagination: {},

            tab: 'leads',
            displayAssignBalancedModal: false,
            displayAssignEvenlyModal: false,

            newLead: {},
            errors: {},
            profile_completion: 52,

            leadSourceDistributionHasData: false,
            leadSourceDistributionChartData: null,

            leadKPIHasData: false,
            leadKPIChartData: null,

            convertedLeadsChartData: {
                labels: ['11/09/2020', '12/09/2020', '13/09/2020', '14/09/2020'],
                datasets: [
                    {
                        label: 'Leads',
                        backgroundColor: ['#3DC4E5', '#3DC4E5', '#3DC4E5', '#3DC4E5', '#3DC4E5', '#3DC4E5'],
                        data: [10, 10, 20, 30, 50, 20,],
                    },
                    {
                        label: 'Converted Leads',
                        backgroundColor: ['#FD7501', '#FD7501', '#FD7501', '#FD7501', '#FD7501', '#FD7501'],
                        data: [5, 10, 15, 20, 50, 10,],
                    }
                ]
            },

            leadSourcesChartData: {
                labels: ['11/09/2020', '12/09/2020', '13/09/2020', '14/09/2020', '15/09/2020', '16/09/2020', '17/09/2020'],
                datasets: [
                    {
                        label: 'Website',
                        backgroundColor: ['#6617ff', '#6617ff', '#6617ff', '#6617ff', '#6617ff', '#6617ff', '#6617ff'],
                        data: [10, 0, 5, 0, 0, 0, 0],
                    },
                    {
                        label: 'Walk-in',
                        backgroundColor: ['#3bc4e5', '#3bc4e5', '#3bc4e5', '#3bc4e5', '#3bc4e5', '#3bc4e5', '#3bc4e5'],
                        data: [0, 3, 0, 0, 0, 5, 5],
                    },
                    {
                        label: 'Phone',
                        backgroundColor: ['#ea50de', '#ea50de', '#ea50de', '#ea50de', '#ea50de', '#ea50de', '#ea50de'],
                        data: [25, 40, 5, 5, 10, 5, 20],
                    },
                    {
                        label: 'Referrals',
                        backgroundColor: ['#f67612', '#f67612', '#f67612', '#f67612', '#f67612', '#f67612', '#f67612'],
                        data: [0, 0, 5, 0, 0, 5, 0],
                    },
                    {
                        label: 'Social Media',
                        backgroundColor: ['#f7be03', '#f7be03', '#f7be03', '#f7be03', '#f7be03', '#f7be03', '#f7be03'],
                        data: [15, 0, 15, 30, 15, 15, 0],
                    },
                    {
                        label: 'Outreach',
                        backgroundColor: ['#abe076', '#abe076', '#abe076', '#abe076', '#abe076', '#abe076', '#abe076'],
                        data: [0, 7, 5, 0, 0, 0, 0],
                    },
                    {
                        label: 'Promotion',
                        backgroundColor: ['#eb7171', '#eb7171', '#eb7171', '#eb7171', '#eb7171', '#eb7171', '#eb7171'],
                        data: [0, 0, 0, 0, 76, 26, 10],
                    }
                ]
            },

            leadSourcesOptions: {
                scales: {
                    yAxes: [{
                        ticks: {
                        beginAtZero: true
                        }
                    }]
                },
                legend: {
                    labels: {

                    }
                }
            },

            displayReassignLeadModal: false,
            displayLeadReassignedModal: false,
            successMessage: null,
            displaySuccessModal: false,
            reassignTo: {},
            reassignableAgents: [],
            reassigningLead: {},

            assignEvenly: [],
            assignBalanced: []
        };
    },

    mounted() {
        this.period = this.$route.params.period ? this.$route.params.period : 'today';
        if(this.$route.hash) {
            this.switchTab(this.$route.hash.substring(1));
        }

        this.loadLeads();
        this.fetchLeadSourceDistribution();
        this.fetchLeadKPIs();
    },

    watch: {
        tab: function (tab) {
            if (tab !== 'source') {
                this.loadLeads();
            }
        }
    },

    methods: {
        /*
         * Set the filter time period based on what period is included in the URL
         * @param {period} string
         */
        setPeriod(period) {
            this.period = period;
            this.fetchLeadSourceDistribution();
            this.fetchLeadKPIs();
            // this.fetchLeadSources();
        },

        /*
         * Fetch all leads.
         * @param {page | optional}
         */
        loadLeads(page) {
            if (typeof page === 'undefined') {
                page = 1;
            }

            var unassigned = this.tab === 'leads' ? 'false' : 'true';

            axios.get('/api/admin/leads/manage/leads?unassigned=' + unassigned + '&page=' + page).then(response => {
                this.leads = response.data.data;
                this.pagination = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => {
                this.loading = false;
            });
        },

        /*
         * Called from the pagination component when user is attempting to go forward or back a page.
         * @param {type} string
         */
        change_page(type) {
            this.loading = true;

            if (type === 'previous') {
                this.loadLeads((this.pagination.current_page - 1));
            } else {
                this.loadLeads((this.pagination.current_page + 1));
            }
        },

        switchTab(tab) {
            this.tab = tab;

            if (this.tab === 'leads') {
            }

            if(this.tab === 'unassigned' || this.href) {
            }

            if(this.tab === 'source') {
            }
        },

        showAddLeadModal() {
            this.displayAddLeadModal = true;
        },

        showAssignBalancedModal() {
            this.displayAssignBalancedModal = true;
            this.processAssignBalanced(true);
        },

        showAssignEvenlyModal() {
            this.displayAssignEvenlyModal = true;
            this.processAssignEvenly(true);
        },

        /*
         * Process leads to assign balacned.
         * @param {dry_run} boolean
         */
        processAssignBalanced(dry_run) {
            console.log('Processing Assign Balacned Leads...');
            axios.post('/api/admin/leads/manage/assign-balanced', {
                dry_run: dry_run
            })
            .then(response => {
                console.log(response.data);
                this.assignBalanced = response.data.dryrun

                if (dry_run == false) {
                    this.displayAssignBalancedModal = false;
                    this.loadLeads();
                    this.showSuccessModal('Those leads have been assigned!');
                }
            })
            .catch(error => {
                console.error(error);
            });
        },

        /*
         * Process leads to assign evenly.
         * @param {dry_run} boolean
         */
        processAssignEvenly(dry_run) {
            console.log('Processing Assign Evenly Leads...');
            axios.post('/api/admin/leads/manage/assign-even', {
                dry_run: true
            })
            .then(response => {
                console.log(response.data);
                this.assignEvenly = response.data.dryrun
            })
            .catch(error => {
                console.error(error);
            });
        },

        hideModals() {
            this.displayAddLeadModal = false;
            this.displayAssignEvenlyModal = false;
        },

        storeLead() {
            console.log(this.newLead);

            axios.post('/api/admin/leads/new', this.newLead)
            .then(response => {
                console.log(response);
                this.hideModals();
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        assignLeadsEvenly() {
            axios.post('/api/admin/leads/manage/assign-even')
            .then(response => {
                console.log(response.data);
                this.hideModals();
                this.showSuccessModal('Those leads have been assigned!');
            })
            .catch(error => {
                console.error(error);
            });
        },
        assignLeadsBalanced() {
            alert("hehehehehe");
        },

        showSuccessModal(message) {
            this.successMessage = message;
            this.displaySuccessModal = true;

            console.log($('.feedback-modal'));
            $('.feedback-modal').css('right', '-340px');

            $('.feedback-modal').animate({
                right: '50px'
            }, {
                complete: function() {
                    this.displaySuccessModal = false;


                    setTimeout(function() {
                        if(!this.displaySuccessModal) {
                            $('.feedback-modal').animate({
                                right: '-340px'
                            });
                        }
                    }.bind(this), 2000);
                }.bind(this)
            });
        },

        /*
         * Fetch raw API chart data for lead source distribution.
         * Loop through the results and create the chart data object.
         * @param {none}
         */
        fetchLeadSourceDistribution() {
            axios.get('/api/admin/leads/manage/graphs/lead-source-distribution/team?period=' + this.period).then(response => {
                var leadSourceLabels = [];
                var leadSourceDatapoints = [];
                var hasData = false;

                response.data.forEach(function(item) {
                    leadSourceLabels.push(item.source);
                    leadSourceDatapoints.push(item.total);

                    if (item.total > 0) {
                        hasData = true;
                    }
                });

                this.leadSourceDistributionHasData = hasData;
                this.leadSourceDistributionChartData = {
                    labels: leadSourceLabels,
                    datasets: [
                        {
                            label: 'Lead Sources',
                            backgroundColor: ['#a377fa', '#3dc4e5', '#ea51de', '#fd7501', '#f7be02', '#b0e679', '#ec7171'],
                            data: leadSourceDatapoints,
                            pointStyle: 'rectRot',
                            weight: 1,
                            borderWidth: 1
                        }
                    ]
                };

                this.componentKey += 1;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        },

        /*
         * Fetch raw API chart data for lead KPIs.
         * Loop through the results and create the chart data object.
         * @param {none}
         */
        fetchLeadKPIs() {
            axios.get('/api/admin/leads/manage/graphs/lead-kpis/team?period=' + this.period).then(response => {

                if (response.data.calls > 0 || response.data.appointments > 0 || response.data.signups > 0) {
                    this.leadKPIHasData = true;
                }

                this.leadKPIChartData = {
                    labels: ['Calls', 'Appointments', 'Sign Ups'],
                    datasets: [
                        {
                            label: 'Lead KPIs',
                            backgroundColor: ['#3DC4E5', '#FD7501', '#F7BE02'],
                            data: [response.data.calls, response.data.appointments, response.data.signups],
                            pointStyle: 'rectRot',
                            weight: 1,
                            borderWidth: 1
                        }
                    ]
                };

                this.componentKey += 1;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        },

        /*
         * Open the reassign lead modal.
         * @param {lead} Object
         */
        openReassignLeadModal(lead) {
            this.reassigningLead = lead;
            this.displayReassignLeadModal = true;
        },

        /*
         * Select agent for lead to be reassigned to.
         * @param {agent} object
         */
        selectReassignAgent(agent) {
            this.reassignTo = agent;
        },

        /*
         * Reassign lead to a new agent.
         * @param {none}
         */
        reassignLead() {
            console.log('Reassigning lead to ', this.reassignTo.id);

            axios.patch('/api/admin/leads/' + this.reassigningLead.id + '/reassign', {agent_id: this.reassignTo.id}).then(response => {
                console.log(response);
                this.displayReassignLeadModal = false;
                this.reassignTo = {};
                this.displayLeadReassignedModal = true;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        }
    }
}

</script>
