<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Log outcome</h3>
            </div>

            <div class="modal__body">
                <div class="form-element">
                    <label class="form-element__label">Did this lead attend the appointment?</label>
                    <div class="form-element__control">
                        <button :class="this.appointment_show === 'Yes' ? 'button' : 'button button--outline'" @click="selectAppointmentShow($event, 'Yes')">
                            Yes
                        </button>

                        <button :class="this.appointment_show === 'No' ? 'button' : 'button button--outline'" @click="selectAppointmentShow($event, 'No')">
                            No
                        </button>
                    </div>
                </div>

                <div class="form-element">
                    <label class="form-element__label">What was the outcome?</label>
                    <div class="form-element__control">
                        <button :class="this.appointment_outcome === 'Not interested' ? 'button' : 'button button--outline'" @click="selectAppointmentOutcome($event, 'Not interested')">
                            Not interested
                        </button>

                        <button :class="this.appointment_outcome === 'Still thinking' ? 'button' : 'button button--outline'" @click="selectAppointmentOutcome($event, 'Still thinking')">
                            Still thinking
                        </button>

                        <button :class="this.appointment_outcome === 'Signed up' ? 'button' : 'button button--outline'" @click="selectAppointmentOutcome($event, 'Signed up')">
                            Signed up
                        </button>
                    </div>
                </div>

                <div class="form-element" v-if="appointment_outcome === 'Not interested'">
                    <label class="form-element__label">
                        Reason
                        <span v-if="this.errors['reason']">{{this.errors['reason'][0]}}</span>
                    </label>
                    <div class="form-element__control">
                        <select class="form-element__select" required v-model="appointment_outcome_reason">
                            <option value disabled selected>Select a Reason</option>
                            <option value="Lacking equipment">Lacking equipment</option>
                            <option value="Spouse declined">Spouse declined</option>
                            <option value="Too far away">Too far away</option>
                            <option value="Poorly maintained">Poorly maintained</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal__footer">
                <button type="button" class="button button--outline" @click="$emit('cancel')">Cancel</button>
                <button class="button" @click="save()" v-if="!this.uploading">Save</button>
                <button class="button" v-else>{{this.uploadPercentage}}%&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
            </div>
        </div>

        <div class="modal__box" v-else>
            <div class="modal__header">
                <h3 class="modal__title">Appointment Updated!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">The log for this appointment was successfully updated.</p>
            </div>


            <div class="modal__footer">
                <button type="button" class="button" @click="displayProcessingModal = false; $emit('complete')">Okay!</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

import VueSlider from 'vue-slider-component';
import 'vue-slider-component/theme/antd.css';

export default {

    components: {
        VueSlider
    },

    props: {
        appointment_id: Number
    },

    data() {
        return {
            leads: [],
            loading: true,
            errors: {},
            newLead: {},
            dailyValue: 0,
            weeklyValue: 0,
            monthlyValue: 0,
            yearlyValue: 0,
            displayProcessingModal: false,
            uploading: false,


            call_outcome: '',

            appointment_show: 'Yes',
            appointment_outcome: 'Not interested',
            appointment_outcome_reason: '',

            subscribe_weekly: false,
            subscribe_monthly: false,
            unsubscribe: false,



            process1: dotsPos => [[0, dotsPos[0], { backgroundColor: '#7a67ad' }]],
        }
    },

    mounted() {
        console.log(this.name);
        if (this.name === 'Calls') {
            console.log('Setting Calls Targets');
            this.dailyValue = this.agent.targets.calls;
            this.weeklyValue = this.agent.targets.calls_weekly;
            this.monthlyValue = this.agent.targets.calls_monthly;
            this.yearlyValue = this.agent.targets.calls_yearly;
        }

        if (this.name === 'Appointments') {
            console.log('Setting Calls Targets');
            this.dailyValue = this.agent.targets.appointments;
            this.weeklyValue = this.agent.targets.appointments_weekly;
            this.monthlyValue = this.agent.targets.appointments_monthly;
            this.yearlyValue = this.agent.targets.appointments_yearly;
        }

        if (this.name === 'Sign Ups') {
            console.log('Setting Sign Up Targets');
            this.dailyValue = this.agent.targets.signups;
            this.weeklyValue = this.agent.targets.signups_weekly;
            this.monthlyValue = this.agent.targets.signups_monthly;
            this.yearlyValue = this.agent.targets.signups_yearly;
        }

        if (this.name === 'Close Ratio') {
            console.log('Setting Close Ratio Targets');
            this.dailyValue = this.agent.targets.close_ratio;
        }
    },

    // updated: function () {
    //     this.$nextTick(function () {
    //         // Code that will run only after the
    //         // entire view has been re-rendered
    //
    //         const year = new Date().getFullYear();
    //         this.years = Array.from({length: year - 1900}, (value, index) => 1901 + index);
    //
    //         this.loadGyms();
    //         this.loadFitnessGoals();
    //     });
    // },

    methods: {

        selectAppointmentShow(event, value) {
            this.appointment_show = value;
        },

        selectAppointmentOutcome(event, value) {
            this.appointment_outcome = value;
        },

        selectCallOutcome(event, value) {
            this.call_outcome = value;
        },

        updateTargets() {
            console.log('Updating targets...');
            this.weeklyValue = this.dailyValue * 7;
            this.monthlyValue = this.dailyValue * 31;
            this.yearlyValue = this.dailyValue * 365;
        },

        save() {
            axios.post('/api/admin/leads/update-appointment/' + this.appointment_id, {
                outcome: this.appointment_outcome,
                show: this.appointment_show,
                reason: this.appointment_outcome_reason
            })
            .then(response => {
                this.finish();
            })
            .catch(error => {
                console.error(error);
            });
        },

        finish() {
            Object.assign(this.$data, this.$options.data());
            this.displayProcessingModal = true;
        },
    }
}
</script>
