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

                    <h2 class="page-header__sub">Day Planner</h2>
                </div>
            </div>
        </section>

        <div class="page-content lead-management day-planner">
            <div class="day-planner__container">
                <div class="day-planner__col">
                    <div class="wrapper">
                        <div class="day-planner__col__header">
                            <button class="button button--outline" @click="selectToday()">Today</button>
                            <label class="lead-management__dashboard__col__label">{{this.selected_date_human}}</label>
                        </div>

                        <div class="lead-management__profile__details__about__box">
                            <calendar :from-date="this.from_date" :attributes="this.day_attributes" v-on:dayclick="selectDate" v-on:update:from-page="changePage" />
                            <div class="form-element">
                                <div class="form-element__checkbox">
                                    <div class="filter-circle filter-circle--orange"></div> Follow up call <input type="checkbox" v-model="filter_followup"/>
                                </div>
                            </div>

                            <div class="form-element">
                                <div class="form-element__checkbox">
                                    <div class="filter-circle filter-circle--blue"></div> Appointment <input type="checkbox" v-model="filter_appointment"/>
                                </div>
                            </div>

                            <div class="form-element">
                                <div class="form-element__checkbox">
                                    <div class="filter-circle filter-circle--green"></div> New lead <input type="checkbox" v-model="filter_newlead"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="day-planner__col">
                    <div class="wrapper">
                        <div class="day-planner__col__header">
                            <label class="lead-management__dashboard__col__label" style="text-transform: uppercase;">
                                {{ this.isToday(this.selected_date) ? "TODAY'S TASKS" : this.selected_date_human + "'S TASKS" }}
                            </label>

                            <button :class="view === 'day' ? 'button' : 'button button--outline'" @click="view = 'day'">Day</button>
                            <button :class="view === 'month' ? 'button' : 'button button--outline'" @click="view = 'month'">Month</button>
                        </div>

                        <div class="day-planner__col__content">
                            <table class="list list--no-top lead-management__dashboard__list day-planner__list" v-if="this.view === 'day' && tasks.length > 0">
                                <thead>
                                    <th>Lead Name</th>
                                    <th>Task</th>
                                    <th @click="sort('datetime')">
                                        Date & Time
                                        <div class="sort-icons">
                                            <i :class="this.sort_field === 'datetime' && this.sort_order === 'ASC' ? 'fas fa-sort-up active' : 'fas fa-sort-up'"></i>
                                            <i :class="this.sort_field === 'datetime' && this.sort_order === 'DESC' ? 'fas fa-sort-down active' : 'fas fa-sort-down'"></i>
                                        </div>
                                    </th>
                                    <th>Calls Made</th>
                                    <th>Details</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    <tr v-for="(task, index) in tasks">
                                        <td>
                                            <router-link :to="'/admin/members/' + task.user_id">
                                                <img :src="task.image_url ? task.image_url : task.lead.image_url"/> {{task.full_name ? task.full_name : task.lead.full_name}}
                                            </router-link>
                                        </td>
                                        <td><div :class="task.type ? 'day-planner__list__type day-planner__list__type--' + task_types[task.type] : 'day-planner__list__type day-planner__list__type--green'">{{task.type ? task.type : 'New lead'}}</div></td>
                                        <td>{{task.datetime_human}}</td>
                                        <td class="lead-management__dashboard__list__call-cell">
                                            <div>
                                                <div v-for="i in 3" :class="getCallsMade(task) >= i ? 'number-indicator active' : 'number-indicator'"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span v-if="task.note_id"><a href="javascript:void(0)" @click="showNoteModal(task)">View note</a></span>
                                            <span v-else-if="task.outcome">{{task.outcome}}</span>
                                            <span v-else>None</span>
                                        </td>
                                        <td>
                                            <span v-if="task.type === 'Appointment' && task.time_passed && !task.outcome">
                                                <a href="javascript:void(0)" @click="showAddAppointmentModal(task)">
                                                    Log outcome
                                                </a>
                                            </span>

                                            <span v-else-if="task.type === 'Follow up call' && task.time_passed && !task.outcome">
                                                <a href="javascript:void(0)" @click="showAddCallModal(task)">
                                                    Log outcome
                                                </a>
                                            </span>

                                            <span v-else>
                                                <router-link :to="'/admin/members/' + task.user_id + '#lead'">
                                                    View profile
                                                </router-link>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <section class="forbidden" v-if="this.view === 'day' && tasks.length === 0">
                            <img src="/images/member-portal/empty.svg">
                            <h2>No Leads</h2>
                            <p>We don't have any leads or tasks to show you for this day.</p>
                        </section>

                        <div v-if="this.view === 'month'" class="lead-management__profile__details__about__box">
                            <calendar ref="monthlyCalendar" :from_date="this.from_date" :attributes="this.month_attributes" :is-expanded="true" :nav-visibility="'hidden'" v-on:dayclick="selectDate"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="feedback-modal">
                <i class="fas fa-check-circle"></i>
                <label>{{this.successMessage}}</label>
            </div>
        </div>

        <log-outcome-modal v-on:cancel="fetchData(); displayAddAppointmentModal = false" v-on:complete="fetchData(); displayAddAppointmentModal = false;" :class="displayAddAppointmentModal ? 'modal modal--active' : 'modal'" :appointment_id="appointment_id" />

        <add-call-modal v-on:cancel="fetchData(); displayAddCallModal = false" v-on:complete="fetchData(); displayAddCallModal = false;" :class="displayAddCallModal ? 'modal modal--active' : 'modal'" :lead_id="lead_id" :full_name="full_name" :image_url="image_url" :calls_made="calls_made" />

        <add-note-modal v-if="note_id" v-on:cancel="fetchData(); displayNotesModal = false" v-on:complete="fetchData(); displayNotesModal = false;" :class="displayNotesModal ? 'modal modal--active' : 'modal'" :edit="false" :note_id="note_id"/>
    </div>
</template>
<script>
import axios from 'axios';
import Calendar from 'v-calendar/lib/components/calendar.umd';
// import DatePicker from 'v-calendar/lib/components/date-picker.umd';

import Datepicker from 'vuejs-datepicker';

import VueTimepicker from 'vue2-timepicker';
import 'vue2-timepicker/dist/VueTimepicker.css';

import AddCallModal from './AddCallModal.vue';
import AddNoteModal from './AddNoteModal.vue';
import LogOutcomeModal from './LogAppointmentOutcomeModal.vue';

export default {
    props: {
        authUser: Object
    },

    components: {
        Calendar,
        Datepicker,
        VueTimepicker,
        LogOutcomeModal,
        AddCallModal,
        AddNoteModal
    },

    mounted() {
        console.log(`Mounted ${this.$route.name}`);
    },

    data() {
        return {
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],

            task_types: {
                "Appointment": "blue",
                "Follow up call": "orange"
            },

            sort_field: 'datetime',
            sort_order: 'DESC',

            tasks: [],

            from_date: new Date(),
            selected_date: new Date(),
            selected_date_human: null,

            highlighted_dates: [],

            filter_followup: true,
            filter_appointment: true,
            filter_newlead: true,

            day_attributes: [
                {
                    // This is the object corresponding to the selected date
                    highlight: 'blue',
                    dates: [],
                },
                {
                    // This is the object corresponding to dots within the calendar
                    dot: 'blue',
                    dates: []
                }
            ],

            month_attributes: [
                {
                    highlight: 'blue',
                    dates: [],
                }
            ],

            errors: {},
            fakeLoading: true,
            loading: true,

            view: 'day',

            displayNotesModal: false,
            displayAddCallModal: false,
            displayAddAppointmentModal: false,
            displaySuccessModal: false,
            displayLogOutcomeModal: false,
            successMessage: null,

            appointment_id: null,
            appointment_show: 'Yes',
            appointment_outcome: 'Not interested',

            lead_id: null,
            full_name: null,
            image_url: null,
            call_id: null,
            call_datetime: null,
            calls_made: null,
            call_outcome: 'Not interested',
            scheduled_call_date: new Date(Date.now()).toDateString(),
            scheduled_call_time: '12:00:00',
            selected_gym: '',
            appointment_duration: '',
            new_note: null,
            note_id: null,


            subscribe_weekly: false,
            subscribe_monthly: false,
            unsubscribe: false,

            gyms: [],
            note: null,

            componentKey: 1,
        }
    },

    watch: {
        filter_appointment: function(value) {
            this.fetchData();
        },
        filter_followup: function(value) {
            this.fetchData();
        },
        filter_newlead: function(value) {
            this.fetchData();
        },
        view: function(value) {
            this.fetchData();
        },
        scheduled_call_date: function(value) {
            this.scheduled_call_date = new Date(this.scheduled_call_date).toDateString();
        }
    },

    methods: {
        fetchData() {
            axios.post('/api/admin/leads/day-planner', {
                filter_appointment: this.filter_appointment,
                filter_followup: this.filter_followup,
                filter_newlead: this.filter_newlead,
                selected_date: this.selected_date,
                sort: {field: this.sort_field, order: this.sort_order}
            })
            .then(response => {
                this.tasks = response.data.tasks;

                this.day_attributes[0].dates = response.data.selected_date;

                this.selected_date_human = response.data.selected_date_human;

                this.day_attributes[1].dates = response.data.month_schedule;

                this.day_attributes = this.day_attributes.filter(function() {
                    return true;
                });

                // for(let task of this.tasks) {
                //     if(task.type === 'Appointment') {
                //         this.month_attributes[1].dates.push(task.datetime);
                //     }
                // }


                this.month_attributes = this.$options.data().month_attributes;
                this.month_attributes[0].dates = response.data.selected_date;

                for(let date of response.data.appointment_schedule) {

                    let time = this.getTimeFromDate(date);

                    this.month_attributes.push({
                        bar: {
                            color: 'blue',
                            class: 'calendar-bar--blue'
                        },
                        dates: [date],
                        popover: {
                            label: `Appointment scheduled at ${time}`
                        }
                    });
                }

                for(let date of response.data.followup_schedule) {
                    let time = this.getTimeFromDate(date);

                    this.month_attributes.push({
                        bar: {
                            color: 'orange',
                            class: 'calendar-bar--orange'
                        },
                        dates: [date],
                        popover: {
                            label: `Follow up call scheduled at ${time}`
                        }
                    });
                }

                for(let date of response.data.newlead_schedule) {
                    let time = this.getTimeFromDate(date);

                    this.month_attributes.push({
                        bar: {
                            color: 'green',
                            class: 'calendar-bar--green'
                        },
                        dates: [date],
                        popover: {
                            label: `New lead created at ${time}`
                        }
                    });
                }
                // this.month_attributes[1].dates = response.data.appointment_schedule;
                // this.month_attributes[1].popover.label = `${response.data.appointment_schedule.length} Appointments are scheduled`;

                // this.month_attributes[2].dates = response.data.followup_schedule;
                // this.month_attributes[2].popover.label = `${response.data.followup_schedule.length} Follow up calls are scheduled`;

                this.month_attributes = this.month_attributes.filter(function() {
                    return true;
                });

                if(this.$refs.monthlyCalendar) this.$refs.monthlyCalendar.move(this.selected_date);
                this.loading = false;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            })
        },

        getCallsMade(task) {
            if(task.calls_made !== undefined) {
                console.log(task.calls_made);
                return task.calls_made;
            }

            console.log(task.lead.calls_made);
            return task.lead.calls_made;
        },

        getTimeFromDate(date) {
            let objDate = new Date(date);

            let hours = objDate.getHours();
            if(hours < 10) {
                hours = `0${hours}`;
            }

            let mins = objDate.getMinutes();
            if(mins < 10) {
                mins = `0${mins}`;
            }

            return `${hours}:${mins}`;
        },

        selectDate(value) {
            this.view = 'day';

            let year = value.date.getFullYear();
            let month = value.date.getMonth() + 1;
            let day = value.date.getDate();

            if(month < 10) {
                month = `0${month}`;
            }

            if(day < 10) {
                day = `0${day}`;
            }


            this.selected_date = new Date(`${year}-${month}-${day}`);
            // this.from_date = this.selected_date;
            this.fetchData();
        },

        selectToday() {
            this.fakeLoading = true;

            this.view = 'day';
            this.from_date = new Date();
            this.selected_date = this.from_date;
        },

        changePage(value) {
            console.log(value);

            let year = value.year;
            let month = value.month;
            if(month < 10) {
                month = `0${month}`;
            }

            // change the page, set the selected date to the first of that month
            // we have this loading hack here because,
            // on the initial load this changePage function gets
            // called by the calendar component.. not sure why
            // but this prevents it from setting the date to the first of the month on the first load
            if(!this.fakeLoading) {
                this.selected_date = new Date(`${year}-${month}-01`);
                if(this.$refs.monthlyCalendar) this.$refs.monthlyCalendar.move(this.selected_date);
            } else {
                this.fakeLoading = false;
            }

            this.fetchData();
        },

        sort(type, order) {
            if(this.sort_field === type) {
                order = this.sort_order === 'DESC' ? 'ASC' : 'DESC';
            } else {
                order = 'DESC';
            }

            this.sort_field = type;
            this.sort_order = order;

            this.fetchData();
        },

        fetchGyms() {
            axios.post('/api/gyms')
            .then(response => {
                this.gyms = response.data;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        showNoteModal(item) {
            // axios.get('/api/leads/notes/' + item.note_id)
            // .then(response => {
            //     this.note = response.data.note;
            // })
            // .catch(error => {
            //     console.error(error);
            //     this.errors = error.response.data.errors;
            // });

            this.note_id = item.note_id;
            this.displayNotesModal = true;
        },

        showAddCallModal(task) {
            // this.fetchGyms();

            this.call_id = task.id;
            this.call_datetime = task.datetime;
            this.lead_id = task.lead.id;
            this.full_name = task.lead.full_name;
            this.image_url = task.lead.image_url;

            this.calls_made = task.lead.calls_made;

            this.displayAddCallModal = true;
        },

        showAddAppointmentModal(task) {
            // this.fetchGyms();

            this.appointment_id = task.id;
            this.displayAddAppointmentModal = true;
        },

        selectAppointmentShow(event, value) {
            this.appointment_show = value;
        },

        selectAppointmentOutcome(event, value) {
            this.appointment_outcome = value;
        },

        selectCallOutcome(event, value) {
            this.call_outcome = value;
        },

        storeAppointmentOutcome() {
            axios.post('/api/leads/update-appointment/' + this.appointment_id, {
                outcome: this.appointment_outcome,
                show: this.appointment_show
            })
            .then(response => {
                this.hideModals();
                this.fetchData();

                this.showSuccessModal('Appointment outcome updated');
            })
            .catch(error => {
                console.error(error);
            });
        },

        storeCall() {
            axios.post('/api/leads/' + this.lead_id + '/log-call', {
                subscribe_weekly: this.subscribe_weekly,
                subscribe_monthly: this.subscribe_monthly,
                unsubscribe: this.unsubscribe,
                outcome: this.call_outcome,
                scheduled_call_date: this.scheduled_call_date,
                scheduled_call_time: this.scheduled_call_time,
                gym_id: this.selected_gym,
                appointment_duration: this.appointment_duration,
                note: this.new_note,
                schedule_id: this.call_id,
                datetime: this.call_datetime
            })
            .then(response => {
                this.hideModals();
                this.fetchData();

                this.showSuccessModal('Call outcome saved');
            })
            .catch(error => {
                console.error(error);
            })
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

        isToday(someDate) {
            let today = new Date()

            return this.selected_date.getDate() == today.getDate() &&
            this.selected_date.getMonth() == today.getMonth() &&
            this.selected_date.getFullYear() == today.getFullYear()
        },

        hideModals() {
            this.displayNotesModal = false;
            this.displayAddCallModal = false;
            this.displayAddAppointmentModal = false;
        }
    }
}
</script>
