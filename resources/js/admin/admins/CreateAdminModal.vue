<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Create Admin</h3>
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

                <div class="row row--form">
                    <div class="six-sm columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                * Is this admin a Sales Agent?
                                <span v-if="this.errors['is_sales_agent']">{{ this.errors['is_sales_agent'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <select required v-model="is_sales_agent">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-element">
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
                <h3 class="modal__title">Admin Created!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">An invitation has been emailed to <strong>{{this.email}}</strong> to confirm their account.</p>
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
            image: [],
            is_sales_agent: 0,

            displayProcessingModal: false
        }
    },

    methods: {
        save() {
            this.uploading = true;

            axios.post('/api/admin/admins', {
                first_name: this.first_name,
                last_name: this.last_name,
                email: this.email,
                phone_number: this.phone_number,
                image: this.image,
                is_sales_agent: this.is_sales_agent
            })
            .then(response => {
                if (this.image.length > 0) {
                    this.uploadImage(response.data.id);
                } else {
                    this.finish();
                }
            })
            .catch(error => {
                this.uploading = false;
                this.errors = error.response.data.errors;
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
                this.finish();
            })
            .catch(function(error){
                this.uploading = false;
                console.log('FAILURE!!');
                console.log(error);
            });
        },

        finish() {
            this.uploading = false;
            this.displayUploadModal = false;
            this.image = [];
            this.name = '';
            this.description = '';
            this.displayProcessingModal = true;
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
