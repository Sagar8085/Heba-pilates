<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Create Member</h3>
            </div>

            <div class="modal__body">
                <div class="row row--form">
                    <div class="six-sm columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                * First Name
                                <span v-if="this.errors['first_name']">{{ this.errors['first_name'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <input type="text" required v-model="first_name">
                            </div>
                        </div>
                    </div>

                    <div class="six-sm columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                * Last Name
                                <span v-if="this.errors['last_name']">{{ this.errors['last_name'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <input type="text" required v-model="last_name">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row row--form">
                    <div class="six-sm columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                * Email Address
                                <span v-if="this.errors['email']">{{ this.errors['email'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <input type="text" required v-model="email">
                            </div>
                        </div>
                    </div>

                    <div class="six-sm columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                * Phone Number
                                <span v-if="this.errors['phone_number']">{{ this.errors['phone_number'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <input type="text" required v-model="phone_number">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-element">
                    <label class="form-element__label">
                        * Date of Birth
                        <span v-if="this.errors['dob_year']">{{ this.errors['dob_year'][0] }}</span>
                    </label>
                    <div class="form-element__control">
                        <div class="row">
                            <div class="four columns" style="margin: 0;">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <input type="number" required v-model="dob_day" placeholder="25" maxlength="2">
                                    </div>
                                </div>
                            </div>

                            <div class="four columns" style="margin: 0;">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <select class="form-element__select" required v-model="dob_month">
                                            <option value disabled selected>-- Month</option>
                                            <option value="01">Jan</option>
                                            <option value="02">Feb</option>
                                            <option value="03">Mar</option>
                                            <option value="04">Apr</option>
                                            <option value="05">May</option>
                                            <option value="06">Jun</option>
                                            <option value="07">Jul</option>
                                            <option value="08">Aug</option>
                                            <option value="09">Sept</option>
                                            <option value="10">Oct</option>
                                            <option value="11">Nov</option>
                                            <option value="12">Dec</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="four columns" style="margin: 0;">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <select class="form-element__select" required v-model="dob_year">
                                            <option value disabled selected>-- Year</option>
                                            <option :value="year" v-for="year in years">{{year}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-element">
                    <label class="form-element__label">
                        * Gender
                        <span v-if="this.errors['gender']">{{ this.errors['gender'][0] }}</span>
                    </label>
                    <div class="form-element__control">
                        <select class="form-element__select" required v-model="gender">
                            <option value selected disabled>Click to select an option</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <!-- <div class="form-element">
                    <span class="form-element__label">
                        Profile Picture
                        <span v-if="this.errors['image']">{{ this.errors['image'][0] }}</span>
                    </span>
                    <div class="form-element__control">
                        <form enctype="multipart/form-data" ref="form">
                            <div class="chat__files" @click="openImageFileBrowser()">
                                <span v-if="this.image.length == 0">Click to Upload Image <img src="/icons/utility/attach_60.png"></span>
                                <span v-else>{{this.image[0].name}}</span>
                            </div>

                            <input type="file" multiple ref="image" id="image" v-on:change="handleImageUpload()" style="display: none;" accept="image/jpg,image/jpeg,image/png,image/webp">
                        </form>
                    </div>
                </div> -->
                <div class="form-element">
                    <label class="form-element__label">
                        * Source or lead
                        <span v-if="this.errors['source']">{{ this.errors['source'][0] }}</span>
                    </label>
                    <div class="form-element__control">
                        <select class="form-element__select" required v-model="source">
                            <option value selected disabled>Click to select an option</option>
                            <option value="referral">Referral</option>
                            <option value="google">Google</option>
                            <option value="social-media">Social media</option>
                            <option value="website">Website</option>
                            <option value="leaflet-flyer">Leaflet / Flyer</option>
                            <option value="local-event">Local Event</option>
                            <option value="local-signs">Local Signs</option>
                            <option value="radio">Radio</option>
                            <option value="tv">TV</option>
                            <option value="returning-guest">Returning Guest</option>
                        </select>
                    </div>
                </div>

                <div class="form-element">
                    <label class="form-element__label">
                        * Which club does this lead come from?
                        <span v-if="this.errors['gym_id']">{{ this.errors['gym_id'][0] }}</span>
                    </label>
                    <div class="form-element__control">
                        <select class="form-element__select" required v-model="gym_id">
                            <option value selected disabled>Click to select an option</option>
                            <option :value="gym.id" v-for="gym in gyms">{{gym.name}}</option>
                        </select>
                    </div>
                </div>

                 <div class="form-element">
                    <label class="form-element__label">
                        * Do you want to send this member and invitation email?
                        <span v-if="this.errors['send_email']">{{ this.errors['send_email'][0] }}</span>
                    </label>
                    <div class="form-element__control">
                        <select class="form-element__select" required v-model="send_email">
                             <option value="">-- Select</option>
                             <option value="yes">Yes</option>
                             <option value="no">No</option>
                        </select>
                    </div>
                </div>
                <div class="info" v-if="this.send_email == 'yes'">This member will be sent an invitation email, to complete their account setup.</div>

                <div class="form-element">
                    <label class="form-element__label">
                        * Fitness Goal
                        <span v-if="this.errors['fitness_goal']">{{ this.errors['fitness_goal'][0] }}</span>
                    </label>
                    <div class="form-element__control">
                        <div class="form-element__checkbox lead-management__profile__fitness-goal" v-for="goal in goals">
                            <input type="checkbox" :value="goal.id.toString()"  v-model="fitness_goal" /> <label class="form-element__label">{{goal.name}}</label>
                        </div>
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
                <h3 class="modal__title">Member Created!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">The Member was successfully created. They will remain an active lead until a subscription or credit pack is purchased.</p>
            </div>


            <div class="modal__footer">
                <button type="button" class="button" @click="displayProcessingModal = false; $emit('complete')">Okay!</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            uploading: false,
            uploadPercentage: 0,
            errors: [],
            gyms: [],
            goals: [],
            years: [],

            first_name: '',
            last_name: '',
            email: '',
            phone_number: '',
            dob_day: '',
            dob_month: '',
            dob_year: '',
            gender: '',
            send_email:'',
            image: [],
            source: '',
            gym_id: '',
            fitness_goal: [],

            displayProcessingModal: false
        }
    },

    mounted() {
        const year = new Date().getFullYear();
        this.years = Array.from({length: year - 1900}, (value, index) => 1901 + index);

        this.loadGyms();
        this.loadFitnessGoals();
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
        save() {
            axios.post('/api/admin/leads/new', {
                first_name: this.first_name,
                last_name: this.last_name,
                email: this.email,
                phone_number: this.phone_number,
                dob_year: this.dob_year,
                dob_month: this.dob_month,
                dob_day: this.dob_day,
                gender: this.gender,
                source: this.source,
                gym_id: this.gym_id,
                send_email: this.send_email,
                fitness_goals: this.fitness_goal
            })
            .then(response => {
                // success
                this.$emit('complete');
                this.finish();
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        loadFitnessGoals() {
            axios.get('/api/admin/leads/focuses')
            .then(response => {
                this.goals = response.data.focuses;
            })
            .catch(error => {
                console.error(error);
            });
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

        finish() {
            Object.assign(this.$data, this.$options.data());
            this.displayProcessingModal = true;
        },
    }
}
</script>
