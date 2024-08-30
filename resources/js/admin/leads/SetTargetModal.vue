<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">{{name}} Target</h3>
            </div>

            <div class="modal__body">
                <div class="form-element">
                   <label class="form-element__label">
                       {{name === 'Close Ratio' ? 'Percentage' : 'Daily'}} target
                   </label>
                   <div class="form-element__control">
                       <vue-slider :min="0" :max="name === 'Close Ratio' ? 100 : 250" v-model="dailyValue" @change="updateTargets()" />
                   </div>
                </div>

                <div class="form-element" v-if="name !== 'Close Ratio'">
                   <div class="form-element__control">
                       <span class="form-element__control__target">
                           <div class="form-element__control__target__text">Weekly</div>{{ weeklyValue }}
                       </span>
                       <span class="form-element__control__target">
                           <div class="form-element__control__target__text">Monthly</div>{{ monthlyValue }}
                       </span>
                       <span class="form-element__control__target">
                           <div class="form-element__control__target__text"><span>&nbsp;&nbsp;Yearly</span></div>{{ yearlyValue }}
                       </span>
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
                <h3 class="modal__title">{{ name }} Target updated!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">yes.</p>
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
        name: String,
        agent: Object
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
        updateTargets() {
            console.log('Updating targets...');
            this.weeklyValue = this.dailyValue * 7;
            this.monthlyValue = this.dailyValue * 31;
            this.yearlyValue = this.dailyValue * 365;
        },

        save() {
            axios.patch('/api/admin/leads/manage/agents/' + this.$route.params.id + '/targets', {
                type: this.name,
                daily: this.dailyValue
            })
            .then(response => {
                this.agent = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.finish());
        },

        finish() {
            Object.assign(this.$data, this.$options.data());
            this.displayProcessingModal = true;
        },
    }
}
</script>
