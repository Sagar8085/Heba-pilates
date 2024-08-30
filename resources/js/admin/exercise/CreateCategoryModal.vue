<template>
    <div>
            <div class="modal__box" v-if="!this.displayProcessingModal">
                <div class="modal__header">
                    <h3 class="modal__title">Create Category</h3>
                </div>

                <div class="modal__body">
                    <div class="form-element">
                        <span class="form-element__label">
                            * Category Name
                            <span v-if="this.errors['name']">{{ this.errors['name'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="text" required v-model="name">
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                            * Description
                            <span v-if="this.errors['description']">{{ this.errors['description'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="text" required v-model="description">
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                            * Image
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
            name: '',
            description: '',
            image: [],
            displayProcessingModal: false
        }
    },

    methods: {
        save() {
            this.uploading = true;

            axios.post('/api/admin/exercise/category', {
                name: this.name,
                description: this.description,
                image: this.image
            })
            .then(response => {
                this.uploadImage(response.data.id);
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
            axios.post('/api/admin/exercise/category/' + resource_id + '/upload-image', formData, {
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
