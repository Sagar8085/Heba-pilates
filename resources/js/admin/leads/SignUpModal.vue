<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Sign Up and Convert to Member</h3>
            </div>

            <div class="modal__body lead-management">

                <div class="lead-management__profile__header">
                    <img class="lead-management__profile__header__image" :src="this.image_url" />
                    <label>{{this.full_name}}</label>
                </div>

                <div class="form-element">
                    <label class="form-element__label">
                        Upload contract (optional)
                        <span v-if="this.errors['contract']">{{this.errors['contract'][0]}}</span>
                    </label>
                    <div class="form-element__control">
                        <form enctype="multipart/form-data" ref="form">
                            <div>
                                <button @click="showContractUploadPopup($event)" v-if="this.contract.length == 0" class="button button--white">Upload document</button>
                                <span v-else>{{this.contract[0].name}}</span>
                            </div>

                            <input type="file" multiple ref="contract" id="contract" v-on:change="handleContractUpload()" style="display: none;">
                        </form>

                    </div>
                </div>
            </div>

            <div class="modal__footer">
                <button type="button" class="button button--outline" @click="$emit('cancel')">Cancel</button>
                <button class="button" @click="save()" v-if="!this.uploading">Convert to Member</button>
                <button class="button" v-else>{{this.uploadPercentage}}%&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
            </div>
        </div>
        <div class="modal__box" v-else>
            <div class="modal__header">
                <h3 class="modal__title">Lead signed up!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">{{ this.success_message }}</p>
            </div>


            <div class="modal__footer">
                <button type="button" class="button" @click="$emit('complete')">Okay!</button>
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
            uploadPercentage: 0,
            displayProcessingModal: false,
            success_message: null,

            signup_keyfob_id: null,
            contract: [],
            signup_date_of_birth: new Date(Date.now()).toDateString(),
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
            this.uploading = true;
            axios.post('/api/admin/leads/' + this.lead_id + '/signup', {
                contract: this.contract,
            })
            .then(response => {
                this.uploadContractFile(response.data.signup.id);
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        finish(success_message) {
            Object.assign(this.$data, this.$options.data());
            this.displayProcessingModal = true;
            this.success_message = success_message;
        },

        uploadContractFile(id) {
            let formData = new FormData();

            /**
             * Iteate over any file sent over appending the files to the form data.
             */
            for(var i = 0; i < this.contract.length; i++ ) {
                let file = this.contract[i];
                formData.append('files[' + i + ']', file);
            }

            axios.post('/api/admin/leads/' + id + '/upload-contract', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: function( progressEvent ) {
                    this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ));
                }.bind(this)
            }).then(response => {
                this.success_message = 'This lead has successfully been converted to a member.';
                this.finish();
            })
            .catch(function(error){
                console.log('FAILURE!!');
                console.log(error);
            });
        },

        showContractUploadPopup(event) {
            event.preventDefault();
            this.$refs.contract.click();
        },

        handleContractUpload() {
            this.contract = this.$refs.contract.files;
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
        }
    }
}

</script>
