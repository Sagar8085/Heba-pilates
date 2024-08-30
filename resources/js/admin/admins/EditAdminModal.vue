<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Edit Admin</h3>
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
                                <input type="text" required v-model="admin.first_name">
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
                                <input type="text" required v-model="admin.last_name">
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
                                <input type="text" required v-model="admin.email">
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
                                <input type="text" required v-model="admin.phone_number">
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
                                <select required v-model="admin.is_sales_agent">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="six-sm columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                Gyms
                                <span v-if="this.errors['gyms']">{{ this.errors['gyms'][0] }}</span>
                            </span>
                            <div class="form-element__control" v-if="this.admin.superadmin">
                                Super admins can access all gyms by default.
                            </div>
                            
                            <div class="form-element__control" v-else-if="this.authUser.superadmin">
                                <multiselect v-model="admin.gyms" :options="gyms" :multiple="true" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Choose one or more gyms" label="name" track-by="id" :preselect-first="false"></multiselect>
                            </div>

                            <div class="form-element__control" v-else-if="!this.authUser.superadmin">
                                Only super admins can edit gym access permissions.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-element">
                    <span class="form-element__label">
                        Profile Picture
                        <span v-if="this.errors['image']">{{ this.errors['image'][0] }}</span>
                    </span>

                    <profile-picture
                        size="large"
                        :image="admin.avatar"
                        :firstName="admin.first_name"
                        :lastName="admin.last_name"
                        />

                    <div class="form-element__control">
                        <form enctype="multipart/form-data" ref="form">
                            <div class="chat__files" @click="openImageFileBrowser()">
                                <span v-if="this.image.length == 0">Click here to replace profile picture <img src="/icons/utility/attach_60.png"></span>
                                <span v-else>To upload this new avatar please click Save</span>
                            </div>

                            <input type="file" multiple ref="image" id="image" v-on:change="handleImageUpload()" style="display: none;" accept="image/jpg,image/jpeg,image/png,image/webp">
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal__footer">
                <button type="button" class="button button--outline button--left" @click="$emit('cancel')">Cancel</button>
                <button class="button" @click="save()" v-if="!this.uploading">Save</button>
                <button class="button" v-else>{{this.uploadPercentage}}%&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
            </div>
        </div>

        <div class="modal__box" v-else>
            <div class="modal__header">
                <h3 class="modal__title">Admin Update!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">{{ this.admin.name }} has been updated</p>
            </div>


            <div class="modal__footer">
                <button type="button" class="button" @click="$emit('complete')">Okay!</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import AdminInput from '../layout/AdminInput.vue'
import AdminModal from '../layout/AdminModal.vue'
import ProfilePicture from '../../components/ProfilePicture.vue';

export default {
    props: {
        admin: Object,
        authUser: Object
    },

    components: { AdminModal, AdminInput, ProfilePicture },

    mounted() {
        this.loadGyms();
    },

    data() {
        return {
            uploading: false,
            uploadPercentage: 0,
            errors: [],

            image: [],

            displayProcessingModal: false,

            newAvatar: {},

            gyms: []
        }
    },

    methods: {
        save() {
            this.uploading = true;

            axios.patch('/api/admin/admins/' + this.admin.id, {
                first_name: this.admin.first_name,
                last_name: this.admin.last_name,
                email: this.admin.email,
                phone_number: this.admin.phone_number,
                image: this.image,
                is_sales_agent: this.admin.is_sales_agent,
                gyms: this.admin.gyms
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
            axios.post('/api/admin/admins/' + resource_id + '/upload-image', formData, {
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
        },

        loadGyms() {
            axios.get('/api/admin/gyms').then(response => {
                this.gyms = response.data;
            })
            .catch(error => {
                console.log('Unable to load gyms at this time.')
            });
        }
    }
}
</script>
