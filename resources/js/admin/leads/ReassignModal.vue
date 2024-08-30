<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Reassign Lead</h3>
            </div>

            <div class="modal__body lead-management">

                <div class="lead-management__profile__header">
                    <img class="lead-management__profile__header__image" :src="this.image_url" />
                    <label>{{this.full_name}}</label>
                </div>

                <div class="reassign-agents">
                    <div :class="reassignTo.id !== undefined && reassignTo.id === agent.id ? 'reassign-agent reassign-agent--selected' : 'reassign-agent'" v-for="agent in this.reassignableAgents" @click="selectReassignAgent(agent)" :key="agent.id">
                        <img class="reassign-agent__avatar" src="/images/logos/heba-logo.png" alt="" title="">
                        <p class="reassign-agent__name">{{agent.name}}</p>
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
                <h3 class="modal__title">Lead Reassigned!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">The lead was successfully reassigned to {{ this.success_message }}</p>
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

            loading: false,
            uploading: false,
            displayProcessingModal: false,
            success_message: null,

            reassignableAgents: [],
            reassignTo: {},
        }
    },

    watch: {
        scheduled_call_date: function(value) {
            this.scheduled_call_date = new Date(this.scheduled_call_date).toDateString();
        },
        lead_id: function(value) {
            this.fetchAssignableAgents(value);
        },
        'this.$route.params.id': function(value) {
            this.fetchAssignableAgents(value);
        }
    },

    mounted() {
        if (this.lead_id) {
            return this.fetchAssignableAgents(this.lead_id);
        }

        return this.$route.params.id ? this.fetchAssignableAgents(this.$route.params.id) : null;
    },

    methods: {
        save() {
            let id = this.lead_id ? this.lead_id : this.$route.params.id;
            axios.patch('/api/admin/leads/' + id + '/reassign', {agent_id: this.reassignTo.id}).then(response => {
                this.finish(this.reassignTo.name);
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        },

        finish(success_message) {
            this.resetData();
            this.displayProcessingModal = true;
            this.success_message = success_message;
        },

        resetData() {
            Object.assign(this.$data, this.$options.data());
            let id = this.lead_id ? this.lead_id : this.$route.params.id;
            this.fetchAssignableAgents(id);
        },

        /*
         * Load agents this lead can be reassigned to.
         * @param {none}
         */
        fetchAssignableAgents(id) {
            axios.get('/api/admin/leads/' + id + '/reassignable-agents').then(response => {
                this.reassignableAgents = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        },

        /*
         * Select agent for lead to be reassigned to.
         * @param {agent} object
         */
        selectReassignAgent(agent) {
            this.reassignTo = agent;
        },
    }
}

</script>
