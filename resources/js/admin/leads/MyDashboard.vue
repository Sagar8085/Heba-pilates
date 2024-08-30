<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">dashboard_customize</i>
                        </div>
                        Lead Management
                    </h1>

                    <h2 class="page-header__sub">My Dashboard</h2>
                </div>
                <div class="page-header__col">
                    <button @click="displayCreateModal = true" class="button">
                        Create Lead
                        <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <div class="lead-management page-content">
            <section class="lead-management__dashboard">
                <div class="wrapper">
                    <div class="lead-management__dashboard__col">
                        <label class="lead-management__dashboard__col__label">DAY PLANNER</label>
                        <div class="lead-management__dashboard__col__box">
                            <label class="title" style="margin-top: 1.5rem;">Welcome back, {{this.authUser.full_name}}</label>
                            <label class="subtitle">Let’s go after those leads!</label>
                            <router-link to="/admin/leads/planner" class="button">Review day planner</router-link>
                        </div>
                    </div>

                    <div class="lead-management__dashboard__col">
                        <label class="lead-management__dashboard__col__label">TARGETS</label>

                        <div class="target-boxes target-boxes--three">
                            <div class="target-boxes__col">
                                <div class="target-box">
                                    <div class="target-box__inner">
                                        <p class="target-box__title">Calls</p>

                                        <div class="target-box__bar" v-tooltip="Math.round((this.calls_made / this.target_calls) * 100) + '% Complete'">
                                            <span :style="'width: ' + (this.calls_made / this.target_calls) * 100 + '%'"></span>
                                        </div>

                                        <div class="row">
                                            <div class="four columns">
                                                <label class="target-box__label">Total</label>
                                                <span class="target-box__value">{{this.calls_made}}</span>
                                            </div>

                                            <div class="four columns center">
                                                <label class="target-box__label">Target</label>
                                                <span class="target-box__value">{{this.target_calls}}</span>
                                            </div>

                                            <div class="four columns right">
                                                <label class="target-box__label">Left</label>
                                                <span class="target-box__value">{{this.remaining_calls > 0 ? this.remaining_calls : 0}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="target-boxes__col">
                                <div class="target-box">
                                    <div class="target-box__inner">
                                        <p class="target-box__title">Appointments</p>

                                        <div class="target-box__bar" v-tooltip="Math.round((this.appointments_made / this.target_appointments) * 100) + '% Complete'">
                                            <span :style="'width: ' + (this.appointments_made / this.target_appointments) * 100 + '%'"></span>
                                        </div>

                                        <div class="row">
                                            <div class="four columns">
                                                <label class="target-box__label">Total</label>
                                                <span class="target-box__value">{{this.appointments_made}}</span>
                                            </div>

                                            <div class="four columns center">
                                                <label class="target-box__label">Target</label>
                                                <span class="target-box__value">{{this.target_appointments}}</span>
                                            </div>

                                            <div class="four columns right">
                                                <label class="target-box__label">Left</label>
                                                <span class="target-box__value">{{this.remaining_appointments > 0 ? this.remaining_appointments : 0}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="target-boxes__col">
                                <div class="target-box">
                                    <div class="target-box__inner">
                                        <p class="target-box__title">Signups</p>

                                        <div class="target-box__bar" v-tooltip="Math.round((this.signups_made / this.target_signups) * 100) + '% Complete'">
                                            <span :style="'width: ' + (this.signups_made / this.target_signups) * 100 + '%'"></span>
                                        </div>

                                        <div class="row">
                                            <div class="four columns">
                                                <label class="target-box__label">Total</label>
                                                <span class="target-box__value">{{this.signups_made}}</span>
                                            </div>

                                            <div class="four columns center">
                                                <label class="target-box__label">Target</label>
                                                <span class="target-box__value">{{this.target_signups}}</span>
                                            </div>

                                            <div class="four columns right">
                                                <label class="target-box__label">Left</label>
                                                <span class="target-box__value">{{this.remaining_signups > 0 ? this.remaining_signups : 0}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lead-management__dashboard__col">
                        <label class="lead-management__dashboard__col__label">RECENT ACHIEVEMENT</label>

                        <div class="lead-management__dashboard__col__box-stats achievement-box empty" v-if="!this.recent_achievement">
                            <img src="/images/icons/img-achievments-emptyState.png" />
                            <label class="subtitle" style="margin-top: 5px; margin-bottom: 0;">You haven't unlocked any achievements yet.</label>
                            <label class="subtitle" style="margin-top: 5px; margin-bottom: 0;">When you do they will appear here.</label>
                        </div>

                        <div class="lead-management__dashboard__col__box achievement-box" v-else>
                            <img :src="this.recent_achievement.details.image_path" height="100" />
                            <div style="padding: 10px;">
                                <label class="title" style="display: block; margin-bottom: 5px;">{{this.recent_achievement.details.name}}</label>
                                <label class="subtitle">{{this.recent_achievement.details.description}}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wrapper">
                    <div class="lead-management__dashboard__col">
                        <label class="lead-management__dashboard__col__label">YOUR RECENTLY ASSIGNED LEADS</label>
                            <div class="list-wrap">

                            <table class="list list--no-top lead-management__dashboard__list" v-if="this.leads.length > 0">
                                <thead>
                                    <th @click="sortLeads('first_name', 'DESC')">
                                        Name
                                        <div class="sort-icons">
                                            <i :class="this.sort_field === 'first_name' && this.sort_order === 'ASC' ? 'fas fa-sort-up active' : 'fas fa-sort-up'"></i>
                                            <i :class="this.sort_field === 'first_name' && this.sort_order === 'DESC' ? 'fas fa-sort-down active' : 'fas fa-sort-down'"></i>
                                        </div>
                                    </th>

                                    <th @click="sortLeads('email', 'DESC')">
                                        Email
                                        <div class="sort-icons">
                                            <i :class="this.sort_field === 'email' && this.sort_order === 'ASC' ? 'fas fa-sort-up active' : 'fas fa-sort-up'"></i>
                                            <i :class="this.sort_field === 'email' && this.sort_order === 'DESC' ? 'fas fa-sort-down active' : 'fas fa-sort-down'"></i>
                                        </div>
                                    </th>

                                    <th @click="sortLeads('phone_number', 'DESC')">
                                        Phone Number
                                        <div class="sort-icons">
                                            <i :class="this.sort_field === 'phone_number' && this.sort_order === 'ASC' ? 'fas fa-sort-up active' : 'fas fa-sort-up'"></i>
                                            <i :class="this.sort_field === 'phone_number' && this.sort_order === 'DESC' ? 'fas fa-sort-down active' : 'fas fa-sort-down'"></i>
                                        </div>
                                    </th>

                                    <th @click="sortLeads('calls_made', 'DESC')">
                                        Calls Made
                                        <div class="sort-icons">
                                            <i :class="this.sort_field === 'calls_made' && this.sort_order === 'ASC' ? 'fas fa-sort-up active' : 'fas fa-sort-up'"></i>
                                            <i :class="this.sort_field === 'calls_made' && this.sort_order === 'DESC' ? 'fas fa-sort-down active' : 'fas fa-sort-down'"></i>
                                        </div>
                                    </th>

                                    <th @click="sortLeads('appointment', 'DESC')">
                                        Appointment
                                        <div class="sort-icons">
                                            <i :class="this.sort_field === 'appointment' && this.sort_order === 'ASC' ? 'fas fa-sort-up active' : 'fas fa-sort-up'"></i>
                                            <i :class="this.sort_field === 'appointment' && this.sort_order === 'DESC' ? 'fas fa-sort-down active' : 'fas fa-sort-down'"></i>
                                        </div>
                                    </th>

                                    <th @click="sortLeads('fitness_goal', 'DESC')">
                                        Fitness Goal
                                        <div class="sort-icons">
                                            <i :class="this.sort_field === 'fitness_goal' && this.sort_order === 'ASC' ? 'fas fa-sort-up active' : 'fas fa-sort-up'"></i>
                                            <i :class="this.sort_field === 'fitness_goal' && this.sort_order === 'DESC' ? 'fas fa-sort-down active' : 'fas fa-sort-down'"></i>
                                        </div>
                                    </th>

                                    <th @click="sortLeads('source', 'DESC')">
                                        Lead Source
                                        <div class="sort-icons">
                                            <i :class="this.sort_field === 'source' && this.sort_order === 'ASC' ? 'fas fa-sort-up active' : 'fas fa-sort-up'"></i>
                                            <i :class="this.sort_field === 'source' && this.sort_order === 'DESC' ? 'fas fa-sort-down active' : 'fas fa-sort-down'"></i>
                                        </div>
                                    </th>
                                </thead>
                                <tbody>
                                    <tr v-for="lead in leads" v-bind:key="lead.id">
                                        <td class="fixed">
                                            <router-link :to="'/admin/members/' + lead.user_id + '#lead'">
                                                <img :src="lead.image_url"/> <div class="lead-management__dashboard__list__name">{{lead.full_name}}</div>
                                            </router-link>
                                        </td>
                                        <td class="fixed">{{lead.email}}</td>
                                        <td>{{lead.phone_number}}</td>
                                        <td class="lead-management__dashboard__list__call-cell">
                                            <div>
                                                <!-- Calls Made -->
                                                <div v-for="i in 3" :class="lead.calls_made >= i ? 'number-indicator active' : 'number-indicator'"></div>
                                            </div>
                                        </td>
                                        <td>{{lead.appointment}}</td>
                                        <td>{{lead.fitness_goal_human}}</td>
                                        <td>{{lead.source}}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <section class="forbidden forbidden--small" v-else>
                                <img src="/images/member-portal/empty.svg">
                                <h2>No Leads</h2>
                                <p>There are not active leads currently assigned to you.</p>
                            </section>
                        </div>
                    </div>
                </div>
            </section>

            <div class="feedback-modal" v-if="this.displaySuccessModal">
                <i class="fas fa-check-circle"></i>
                <label>{{this.successMessage}}</label>
            </div>
        </div>

        <create-lead-modal v-on:cancel="displayCreateModal = false" v-on:complete="displayCreateModal = false; loadDashboard()" :class="displayCreateModal ? 'modal modal--active' : 'modal'" />
    </div>
</template>
<script>

import axios from 'axios';
import Datepicker from 'vuejs-datepicker';
import Vue from 'vue'
import { VTooltip, VPopover, VClosePopover } from 'v-tooltip'
import CreateLeadModal from './CreateLeadModal.vue';

Vue.directive('tooltip', VTooltip)
Vue.directive('close-popover', VClosePopover)
Vue.component('v-popover', VPopover)

Vue.use(VTooltip)

export default {

    props: {
        authUser: Object
    },

    components: {
        CreateLeadModal,
        Datepicker
    },

    data() {
        return {
            leads: [],

            sort_field: 'created_at',
            sort_order: 'desc',

            target_calls: 0,
            calls_made: 0,
            remaining_calls: 0,

            target_appointments: 0,
            appointments_made: 0,
            remaining_appointments: 0,

            target_signups: 0,
            signups_made: 0,
            remaining_signups: 0,

            recent_achievement: null,

            displayCreateModal: false,
            displaySuccessModal: false,
        }
    },

    mounted() {
        console.log(`Mounted ${this.$route.name}`);
        this.loadDashboard();
    },

    methods: {
        loadDashboard() {
            let sortData = {field: this.sort_field, order: this.sort_order};
            if(!this.sort_field || !this.sort_order) {
                sortData = null;
            }
            axios.post('/api/admin/leads', {status: 'new', sort: sortData})
            .then(response => {
                this.leads = response.data.leads;
                console.log(this.leads);

                this.target_calls = response.data.target_calls;
                this.calls_made = response.data.calls_made;
                this.remaining_calls = this.target_calls - this.calls_made;

                this.target_appointments = response.data.target_appointments;
                this.appointments_made = response.data.appointments_made;
                this.remaining_appointments = this.target_appointments - this.appointments_made;

                this.target_signups = response.data.target_signups;
                this.signups_made = response.data.signups_made;
                this.remaining_signups = this.target_signups - this.signups_made;

                this.recent_achievement = response.data.recent_achievement;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            })
            .finally(() => {
                this.loading = false;
            });
        },

        sortLeads(field, order) {
            if(this.sort_field === field) {
                order = this.sort_order === 'DESC' ? 'ASC' : 'DESC';
            }

            this.sort_field = field;
            this.sort_order = order;

            this.loadDashboard();
        },
    }
}
</script>
