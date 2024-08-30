<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Log call activity</h3>
            </div>

            <div class="modal__body lead-management">

                <div class="lead-management__profile__header">
                    <img class="lead-management__profile__header__image" :src="this.image_url" />
                    <label>{{this.full_name}}</label>
                </div>

                <div class="form-element">
                    <label class="form-element__label">What was the outcome?</label>
                    <div class="form-element__control">
                        <button :class="this.call_outcome === 'Not interested' ? 'button' : 'button button--outline'" @click="selectCallOutcome($event, 'Not interested')">
                            Not interested
                        </button>
                        &nbsp;
                        <button :class="this.call_outcome === 'Still thinking' ? 'button' : 'button button--outline'" @click="selectCallOutcome($event, 'Still thinking')">
                            Still thinking
                        </button>
                        &nbsp;
                        <button :class="this.call_outcome === 'Appointment' ? 'button' : 'button button--outline'" @click="selectCallOutcome($event, 'Appointment')">
                            Appointment
                        </button>
                        &nbsp;
                        <button :class="this.call_outcome === 'No answer' ? 'button' : 'button button--outline'" @click="selectCallOutcome($event, 'No answer')">
                            No answer
                        </button>
                    </div>
                </div>

                <div v-if="this.call_outcome === 'Appointment'" class="form-element">
                    <label class="form-element__label">
                        Which gym is the appointment for?
                        <span v-if="this.errors['gym_id']">{{ this.errors['gym_id'][0] }}</span>
                    </label>
                    <div class="form-element__control">
                        <select class="form-element__select" required v-model='selected_gym'>
                            <option value disabled selected>Please select a gym</option>
                            <option v-for="gym in gyms" :value="gym.id">{{gym.name}}</option>
                        </select>
                    </div>
                </div>

                <div v-if="this.call_outcome === 'Still thinking' || this.call_outcome === 'Appointment'" class="form-element" style="display: inline-block;">
                    <label v-if="this.call_outcome === 'Still thinking'" class="form-element__label">Reschedule Call</label>
                    <label v-else class="form-element__label">Select date</label>
                    <div class="form-element__control" style="position: relative;">
                        <datepicker
                            input-class="form-element__datepicker"
                            :value="scheduled_call_date"
                            v-model="scheduled_call_date"
                        />

                        <img src="/images/icons/expand_more-24px.svg" width="24px" style="position: absolute; top: calc(50% - 12px); right: 0;" />
                    </div>

                    <label class="form-element__label">Select start time</label>
                    <div class="form-element__control" style="position: relative;">
                        <vue-timepicker
                            placeholder="Select a Time"
                            hour-label="Hour"
                            minute-label="Minute"
                            input-class="form-element__datepicker"
                            input-width="100%"
                            v-model="scheduled_call_time"
                        />

                        <img v-if="!scheduled_call_time" src="/images/icons/expand_more-24px.svg" width="24px" style="position: absolute; top: calc(50% - 12px); right: 0;" />
                    </div>
                </div>

                <div class="form-element" v-if="this.call_outcome === 'Appointment'">
                    <label class="form-element__label">
                        Duration
                        <span v-if="this.errors['appointment_duration']">{{this.errors['appointment_duration'][0]}}</span>
                    </label>
                    <div class="form-element__control">
                        <select class="form-element__select" required v-model="appointment_duration">
                            <option value disabled selected>Select a Duration</option>
                            <option v-for="i in 3" :value="i">{{ i }} Hour{{i > 1 ? 's' : ''}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-element">
                    <label class="form-element__label">
                        Notes
                        <span v-if="this.errors['note']">{{this.errors['note'][0]}}</span>
                    </label>
                    <div class="form-element__control">
                        <textarea class="lead__profile__notes" placeholder="Add notes here" v-model="new_note"></textarea>
                    </div>
                </div>

            </div>

            <div class="modal__footer">
                <button type="button" class="button button--outline" @click="resetData(); $emit('cancel')">Cancel</button>
                <button class="button" @click="save()" v-if="!this.uploading">Save</button>
                <button class="button" v-else>{{this.uploadPercentage}}%&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
            </div>
        </div>

        <div class="modal__box" v-else>
            <div class="modal__header">
                <h3 class="modal__title">Activity logged!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">{{ this.success_message }}</p>
            </div>


            <div class="modal__footer">
                <button type="button" class="button" @click="resetData(); $emit('complete')">Okay!</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

import Datepicker from 'vuejs-datepicker';
import VueTimepicker from 'vue2-timepicker';
import 'vue2-timepicker/dist/VueTimepicker.css';

export default {

    components: {
        Datepicker, VueTimepicker
    },

    props: { full_name: String, image_url: String, calls_made: Number, lead_id: Number },

    data() {
        return {
            uploading: false,
            uploadPercentage: 0,
            errors: [],

            displayProcessingModal: false,
            success_message: '',

            call_outcome: 'Not interested',
            subscribe_weekly: null,
            subscribe_monthly: null,
            unsubscribe: null,
            new_note: null,

            scheduled_call_date: new Date(Date.now()).toDateString(),
            scheduled_call_time: '12:00:00',

            appointment_about: null,
            appointment_duration: '',
            appointment_notes: null,

            gyms: [],
            selected_gym: '',
        }
    },

    mounted() {
        this.loadGyms();
    },

    watch: {
        subscribe_weekly: function(value) {
            if(value === true) {
                this.unsubscribe = false;
            }
        },
        subscribe_monthly: function(value) {
            if(value === true) {
                this.unsubscribe = false;
            }
        },
        unsubscribe: function(value) {
            if(value === true) {
                this.subscribe_weekly = false;
                this.subscribe_monthly = false;
            }
        },
        scheduled_call_date: function(value) {
            this.scheduled_call_date = new Date(this.scheduled_call_date).toDateString();
        },
    },

    methods: {
        save() {
            axios.post('/api/admin/leads/' + this.lead_id + '/log-call', {
                subscribe_weekly: this.subscribe_weekly,
                subscribe_monthly: this.subscribe_monthly,
                unsubscribe: this.unsubscribe,
                outcome: this.call_outcome,
                scheduled_call_date: this.scheduled_call_date,
                scheduled_call_time: this.scheduled_call_time,
                gym_id: this.selected_gym,
                appointment_duration: this.appointment_duration,
                note: this.new_note
            })
            .then(response => {
                switch(this.call_outcome) {
                    case 'Appointment':
                        this.success_message = 'An appointment has been scheduled';
                        break;
                    case 'Still thinking':
                        this.success_message = 'A callback has been scheduled';
                        break;
                    default:
                        this.success_message = 'Call activity saved';
                        break;
                }

                this.finish(this.success_message);
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        finish(success_message) {
            this.resetData();
            this.displayProcessingModal = true;
            this.success_message = success_message;
        },

        resetData() {
            Object.assign(this.$data, this.$options.data());
        },

        selectCallOutcome(event, value) {
            this.call_outcome = value;
        },

        /*
         * Fetch gyms for listing.
         */
        loadGyms() {
            axios.get('/api/admin/gyms')
                .then(response => {
                    this.gyms = response.data;
                })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error)
                })
                .finally(() => this.loading = false);
        },
    }
}
</script>
