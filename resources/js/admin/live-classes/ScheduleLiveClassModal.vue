<template>
    <div>
        <div class="modal__box" v-if="!created">
            <div class="modal__header">
                <h3 class="modal__title">Schedule Live Class</h3>
            </div>

            <div class="modal__body">
                <div class="row">
                    <div class="six columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                * Choose a category
                                <span v-if="this.errors['category_id']">{{ this.errors['category_id'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <select v-model="category_id">
                                    <option value="">-- Select --</option>
                                    <option :value="category.id" v-for="category in categories">{{ category.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="six columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                * How long will this class last?
                                <span v-if="this.errors['duration']">{{ this.errors['duration'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <select v-model="duration">
                                    <option value="">-- Select --</option>
                                    <option value="30">30 minutes</option>
                                    <option value="45">45 minutes</option>
                                    <option value="60">1 hour</option>
                                    <option value="90">1 hour 30 minutes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="twelve columns">
                        <div :class="this.category_id !== '' && this.duration !== '' ? 'form-element' : 'form-element form-element--blur'">
                            <span class="form-element__label">
                                * Does this class require use of a Heba Pilates Reformer Machine?
                                <span v-if="this.errors['reformer']">{{ this.errors['reformer'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <select v-model="reformer">
                                    <option value="">-- Select --</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="twelve columns">
                        <div :class="this.category_id !== '' && this.duration !== '' ? 'form-element' : 'form-element form-element--blur'">
                            <span class="form-element__label">
                                * Choose an instructor (this will not be shown to the member)
                                <span v-if="this.errors['instructor_id']">{{ this.errors['instructor_id'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <select v-model="instructor_id">
                                    <option value="">-- Select --</option>
                                    <option :value="instructor.id" v-for="instructor in instructors">{{ instructor.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="six columns">
                        <div :class="this.category_id !== '' && this.duration !== '' && this.instructor_id !== '' ? 'form-element' : 'form-element form-element--blur'">
                            <span class="form-element__label">
                                * Now choose a date
                                <span v-if="this.errors['date']">{{ this.errors['date'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <datepicker
                                    title-position="left"
                                    v-model="date"
                                    mode="single"
                                    :inline="true"
                                    @selected="selectAttribute"
                                    :min-date='new Date(Date.now())'
                                />
                            </div>
                        </div>
                    </div>

                    <div class="six columns">
                        <div :class="this.date !== '' ? 'form-element' : 'form-element form-element--blur'">
                            <span class="form-element__label">
                                * Now choose the time this class will start
                                <span v-if="this.errors['time_hour']">{{ this.errors['time_hour'][0] }}</span>
                                <span v-if="this.errors['time_minute']">{{ this.errors['time_minute'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <div class="row">
                                    <div class="five columns">
                                        <select v-model="time_hour">
                                            <option value="" selected disabled>Hour</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16">16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>
                                            <option value="19">19</option>
                                            <option value="20">20</option>
                                            <option value="21">21</option>
                                            <option value="22">22</option>
                                            <option value="23">23</option>
                                        </select>
                                    </div>

                                    <div class="two columns" style="text-align: center; padding-top: 7px;">:</div>

                                    <div class="five columns">
                                        <select v-model="time_minute">
                                            <option value="" selected disabled>Minute</option>
                                            <option value="00">00</option>
                                            <option value="15">15</option>
                                            <option value="30">30</option>
                                            <option value="45">45</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal__footer">
                <button type="button" class="button button--left button--outline" @click="$emit('cancel')">Cancel</button>
                <button class="button" @click="save()" v-if="!this.uploading">Save</button>
                <button class="button" v-else><i class="fas fa-spinner fa-spin"></i></button>
            </div>
        </div>

        <div class="modal__box" v-else>
            <div class="modal__header">
                <h3 class="modal__title">Live Class Scheuled</h3>
            </div>

            <div class="modal__body">
                Great! Your live class has now been scheduled and members can now book their slots.
            </div>

            <div class="modal__footer">
                <button type="button" class="button button--outline" @click="$emit('complete')">Okay</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Datepicker from 'vuejs-datepicker';

export default {
    components: { Datepicker },

    data() {
        return {
            created: false,
            uploading: false,

            errors: [],
            categories: [],
            instructors: [],

            category_id: '',
            reformer: '',
            instructor_id: '',
            duration: '',
            date: '',
            time_hour: '',
            time_minute: ''
        }
    },

    mounted() {
        this.loadCategories();
        this.loadInstructors();
    },

    methods: {
        selectAttribute(date) {
            console.log('date selected: ' + date);
        },

        loadCategories() {
            axios.get('/api/admin/live-classes/categories').then(response => {
                this.categories = response.data;
            });
        },

        loadInstructors() {
            axios.get('/api/admin/live-classes/instructors').then(response => {
                this.instructors = response.data;
            });
        },

        save() {
            this.uploading = true;

            axios.post('/api/admin/live-classes', {
                category_id: this.category_id,
                date: this.date,
                time_hour: this.time_hour,
                time_minute: this.time_minute,
                duration: this.duration,
                instructor_id: this.instructor_id,
                reformer: this.reformer
            })
            .then(response => {
                console.log('');
                this.created = true;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            }).finally(() => this.uploading = false);
        }
    }
}
</script>
