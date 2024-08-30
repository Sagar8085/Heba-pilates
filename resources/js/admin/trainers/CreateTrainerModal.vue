<template>
    <div>
            <div class="modal__box" v-if="!this.displayProcessingModal">
                <div class="modal__header">
                    <h3 class="modal__title">Create Trainer</h3>
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
                        <span class="form-element__label">
                            * Biography <br><small class="extra-small">({{biography.length}} / 2000)</small>
                            <span v-if="this.errors['biography']">{{ this.errors['biography'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <textarea required v-model="biography" rows="5" placeholder="John is a very capable personal trainer with over 5 years experience, training some of the most well known celebrities in the UK. His qualifications are..."></textarea>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                             Specialisations
                            <span v-if="this.errors['specialisations']">{{ this.errors['specialisations'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <div>
                              <multiselect v-model="selected_specialisations" :options="specialisations" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose some specialisations" label="name" track-by="id" :preselect-first="false"></multiselect>
                            </div>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                            * Profile Picture
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
                    <h3 class="modal__title">File Processing</h3>
                </div>

                <div class="modal__body">
                    <p class="modal__text">Your file has been queued for processing, this is mandatory so we can reduce the file size and strip un-unnecessary bites from your file to make video load times faster for customers and reduce their bandwidth.</p>
                </div>


                <div class="modal__footer">
                    <button type="button" class="button" @click="$emit('complete')">Okay!</button>
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

            first_name: '',
            last_name: '',
            email: '',
            phone_number: '',
            biography: '',
            image: [],

            specialisations: [
                {
                    "name": "Functional Training"
                },
                {
                    "name": "Kettlebell Training"
                },
                {
                    "name": "Battling Ropes Traiing"
                },
                {
                    "name": "Boxing"
                },
                {
                    "name": "Muscle Gain"
                },
            ],

            selected_specialisations: [],

            displayProcessingModal: false
        }
    },

    mounted() {
        this.getSpecialisations();
    },

    methods: {
        save() {
            this.uploading = true;

            axios.post('/api/admin/trainers', {
                first_name: this.first_name,
                last_name: this.last_name,
                email: this.email,
                phone_number: this.phone_number,
                biography: this.biography,
                image: this.image,
                specialisations: this.selected_specialisations
            })
            .then(response => {
                this.uploadImage(response.data.id);
            })
            .catch(error => {
                this.uploading = false;
                this.errors = error.response.data.errors;
            });
        },

        getSpecialisations(){
            axios.get('/api/admin/trainers/specialisations').then(response => {
                this.specialisations = response.data;
                console.log(response);
            });
        },

        uploadImage(resource_id) {
            console.log('Uploading image....');

            /*
             Initialize the form data
             */
            let formData = new FormData();

            /*
             * Iteate over any file sent over appending the files to the form data.
             */
            for( var i = 0; i < this.image.length; i++ ){
                let file = this.image[i];
                formData.append('files[' + i + ']', file);
            }

            console.log(formData);

            /*
             * Make the request to the POST /multiple-files URL
             */
            axios.post('/api/admin/trainers/' + resource_id + '/upload-image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.uploading = false;
                this.displayUploadModal = false;
                this.image = [];
                this.name = '';
                this.description = '';
                this.displayProcessingModal = true;
            })
            .catch(function(error){
                this.uploading = false;
                console.log('FAILURE!!');
                console.log(error);
            });
        },

        openImageFileBrowser() {
            this.$refs.image.click();
        },

        handleImageUpload() {
            this.image = this.$refs.image.files;
            console.log(this.image);
        }
    }
}
</script>
