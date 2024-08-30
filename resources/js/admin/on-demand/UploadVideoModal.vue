<template>
    <div>
            <div class="modal__box" v-if="!this.displayProcessingModal">
                <div class="modal__header">
                    <h3 class="modal__title">Upload Video</h3>
                </div>

                <div class="modal__body">
                    <div class="row row--form">
                        <div class="six columns">
                            <div class="form-element">
                                <span class="form-element__label">
                                    * Name of class
                                    <span v-if="this.errors['name']">{{ this.errors['name'][0] }}</span>
                                </span>
                                <div class="form-element__control">
                                    <input type="text" required v-model="name">
                                </div>
                            </div>
                        </div>

                        <div class="six columns">
                            <div class="form-element">
                                <span class="form-element__label">
                                    * Duration (minutes)
                                    <span v-if="this.errors['duration']">{{ this.errors['duration'][0] }}</span>
                                </span>
                                <div class="form-element__control">
                                    <input type="tel" required v-model="duration" placeholder="25">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                            * Description
                            <span v-if="this.errors['description']">{{ this.errors['description'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <textarea rows="5" type="text" required v-model="description"></textarea>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                             Category
                            <span v-if="this.errors['description']">{{ this.errors['description'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <div>
                              <multiselect v-model="selected_categories" :options="categories" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose a category" label="name" track-by="slug" :preselect-first="false"></multiselect>
                            </div>
                        </div>
                    </div>
                    <div class="form-element">
                        <span class="form-element__label">
                             Visibility
                            <span v-if="this.errors['published']">{{ this.errors['published'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <div>
                                <select v-model="published">
                                    <option value="" selected disabled>-- Select ---</option>
                                    <option value="1">Published</option>
                                    <option value="0">Hidden</option>
                                </select>
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="six columns">
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

                        <div class="six columns">
                            <div class="form-element">
                                <span class="form-element__label">
                                    * Recording
                                    <span v-if="this.errors['video']">{{ this.errors['video'][0] }}</span>
                                </span>
                                <div class="form-element__control">
                                    <form @submit="uploadFiles()" enctype="multipart/form-data" ref="form">
                                        <div class="chat__files" @click="openVideoFileBrowser()">
                                            <span v-if="this.video.length == 0">Click to Upload Video <img src="/icons/utility/attach_60.png"></span>
                                            <span v-else>{{this.video[0].name}}</span>
                                        </div>

                                        <input type="file" multiple ref="video" id="video" v-on:change="handleVideoUpload()" style="display: none;" accept="video/mp4">
                                    </form>
                                </div>
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
            duration: '',
            description: '',
            published: 1,
            image: [],
            video: [],
            displayProcessingModal: false,
            selected_categories: [],
            categories: [],
            price: ''
        }
    },

    methods: {
        save() {
            this.uploading = true;

            axios.post('/api/admin/on-demand/library', {
                name: this.name,
                description: this.description,
                image: this.image,
                video: this.video,
                duration: this.duration,
                categories: this.selected_categories,
                published: this.published
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
            axios.post('/api/admin/on-demand/library/' + resource_id + '/upload-image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.uploadVideo(resource_id);
            })
            .catch(function(error){
                console.log('FAILURE!!');
                console.log(error);
            });
        },

        uploadVideo(resource_id) {
            console.log('Uploading video: ' + resource_id)
        },

        openImageFileBrowser() {
            this.$refs.image.click();
        },

        handleImageUpload() {
            this.image = this.$refs.image.files;
            console.log(this.image);
        },

        openVideoFileBrowser() {
            this.$refs.video.click();
        },

        handleVideoUpload() {
            this.video = this.$refs.video.files;
            console.log(this.video);
        },

        uploadVideo(resource_id) {
            var _this = this;

            console.log('Uploading video....');
            /**
             Initialize the form data
             */
            let formData = new FormData();

            /**
             * Iteate over any file sent over appending the files to the form data.
             */
            for( var i = 0; i < this.video.length; i++ ){
                let file = this.video[i];
                formData.append('files[' + i + ']', file);
            }

            console.log(formData);

            /**
             * Make the request to the POST /multiple-files URL
             */
            axios.post('/api/admin/on-demand/library/' + resource_id + '/upload-video', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: function( progressEvent ) {
                    this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ));
                }.bind(this)
            }).then(response => {
                this.uploading = false;
                this.displayUploadModal = false;
                this.video = [];
                this.image = [];
                this.name = '';
                this.description = '';
                this.displayProcessingModal = true;
            })
            .catch(function(error){
                // _this.deleteOnDemandClass(recording.id);
                console.log(error);
                _this.uploading = false;
                alert('We couldn\'t complete your upload as your network connection dropped during the upload process. If you require assistance with an upload please contact support@volution.fit');
                // window.location.reload();
            });
        },
        getCategories(){
            axios.get('/api/admin/on-demand/categories').then(response => {
                this.categories = response.data.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
        }
    },
    mounted() {
        this.getCategories();
    }
}
</script>
