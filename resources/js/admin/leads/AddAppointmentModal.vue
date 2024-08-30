<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Book an Appointment</h3>
            </div>

            <div class="modal__body lead-management">

                <div class="lead-management__profile__header">
                    <img class="lead-management__profile__header__image" :src="this.image_url" />
                    <label>{{this.full_name}}</label>
                </div>

                <div class="form-element">
                    <label class="form-element__label">
                        Which gym is the appointment for?
                        <span v-if="this.errors['gym_id']">{{this.errors['gym_id'][0]}}</span>
                    </label>
                    <div class="form-element__control">
                        <select class="form-element__select" required v-model='selected_gym'>
                            <option value disabled selected>Please select a gym</option>
                            <option v-for="gym in gyms" v-bind:key="gym.id" :value="gym.id">{{gym.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="six columns">
                        <div class="form-element">
                            <label class="form-element__label"><span v-if="this.errors['scheduled_date']">{{this.errors['scheduled_date'][0]}}</span>Select date</label>
                            <div class="form-element__control" style="position: relative;">
                                <datepicker
                                    input-class="form-element__datepicker"
                                    :value="scheduled_call_date"
                                    v-model="scheduled_call_date"
                                    :disabled-dates="{to: new Date()}"
                                />


                                <img src="/images/icons/expand_more-24px.svg" width="24px" style="position: absolute; top: calc(50% - 12px); right: 0;" />
                            </div>
                        </div>
                    </div>
                    <div class="six columns">
                        <div class="form-element">
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
                    </div>
                </div>

                <div class="row">
                    <div class="six columns">
                        <div class="form-element">
                            <label class="form-element__label">
                                Duration
                                <span v-if="this.errors['duration']">{{this.errors['duration'][0]}}</span>
                            </label>
                            <div class="form-element__control">
                                <select class="form-element__select" required v-model="appointment_duration">
                                    <option value disabled selected>Select a Duration</option>
                                    <option v-for="i in 3" :value="i">{{ i }} Hour{{i > 1 ? 's' : ''}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="six columns">
                        <div class="form-element">
                            <label class="form-element__label">
                                Guide
                                <span v-if="this.errors['guide']">{{this.errors['guide'][0]}}</span>
                            </label>
                            <div class="form-element__control">
                                <input type="text" placeholder="John Appleseed" v-model="guide">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-element">
                    <label class="form-element__label">
                        Notes
                        <span v-if="this.errors['note']">{{this.errors['note'][0]}}</span>
                    </label>
                    <div class="form-element__control">
                        <textarea class="lead__profile__notes" rows=10 placeholder="Add notes here" v-model="new_note"></textarea>
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
                <h3 class="modal__title">Appointment scheduled!</h3>
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

    props: { full_name: String, image_url: String, lead_id: Number },

    data() {
        return {
            new_note: null,
            note: {},

            gyms: [],

            errors: {},
            selected_gym: '',
            scheduled_call_date: new Date(Date.now()).toDateString(),
            scheduled_call_time: '12:00:00',
            appointment_about: null,
            appointment_duration: '',
            appointment_notes: null,
            guide: '',

            loading: false,
            uploading: false,
            displayProcessingModal: false,
            success_message: null,
        }
    },

    watch: {
        scheduled_call_date: function(value) {
            this.scheduled_call_date = new Date(this.scheduled_call_date).toDateString();
        }
    },

    mounted() {
        this.loadGyms();
    },

    methods: {
        save() {
            axios.post('/api/admin/leads/' + this.lead_id + '/appointment', {
                gym_id: this.selected_gym,
                scheduled_date: this.scheduled_call_date,
                scheduled_time: this.scheduled_call_time,
                duration: this.appointment_duration,
                note: this.new_note,
                guide: this.guide
            })
            .then(response => {
                this.finish('The Appointment was successfully scheduled.');
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

            return true;
        }
    }
}

</script>
