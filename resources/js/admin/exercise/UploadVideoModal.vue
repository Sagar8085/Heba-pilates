<template>
    <div>
            <div class="modal__box" v-if="!this.displayProcessingModal">
                <div class="modal__header">
                    <h3 class="modal__title">Upload Exercise</h3>
                </div>

                <div class="modal__body">
                    <div class="form-element">
                        <span class="form-element__label">
                            * Name of Video / Class
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
                            * Paid only (don't include in webportal create workout, checked = paid only)
                            <span v-if="this.errors['paid']">{{ this.errors['paid'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input id="paid_checkbox" class="checkbox" type="checkbox" @click="togglePaid()" v-model="paid">
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                            * Default Duration (seconds)
                            <span v-if="this.errors['duration']">{{ this.errors['duration'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="number" required v-model="duration">
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                            * Duration Per Rep (How long does a single rep take to completion in seconds)
                            <span v-if="this.errors['duration_per_rep']">{{ this.errors['duration_per_rep'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="number" required v-model="duration_per_rep">
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                             Intensity*
                            <span v-if="this.errors['intensities']">{{ this.errors['intensities'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <div>
                                <multiselect v-model="selected_intensity" deselect-label="Can't remove this value" track-by="id" label="intensity" placeholder="Select one" :options="intensities" :searchable="false" :allow-empty="false"></multiselect>
                            </div>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                             Focuses
                            <span v-if="this.errors['focuses']">{{ this.errors['focuses'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <div>
                              <multiselect v-model="selected_focuses" :options="focuses" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose which focus" label="name" track-by="id" :preselect-first="false"></multiselect>
                            </div>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                             Sections
                            <span v-if="this.errors['sections']">{{ this.errors['sections'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <div>
                              <multiselect v-model="selected_sections" :options="sections" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose which sections" label="name" track-by="id" :preselect-first="false"></multiselect>
                            </div>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                             Categories
                            <span v-if="this.errors['categories']">{{ this.errors['categories'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <div>
                              <multiselect v-model="selected_categories" :options="categories" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose some categories" label="name" track-by="slug" :preselect-first="false"></multiselect>
                            </div>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                            Guide
                            <span v-if="this.errors['description']">{{ this.errors['description'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <table class="list list--no-top list--no-bottom list--new list--side">
                                <tbody>
                                    <tr  v-for="(step, counter) in steps" v-bind:key="counter">
                                        <td class="no-border no-left textarea-input"><textarea class="deletable" type="text" required v-model="step.text"></textarea></td>
                                        <td class="no-border"><span class="button button--red" @click="removeStep(counter)"> Delete </span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="">
                            <button class="button" @click="addStep()"> Add Step </button>

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

                                <input type="file" multiple ref="image"  v-on:change="handleImageUpload()" style="display: none;" accept="image/jpg,image/jpeg,image/png,image/webp">
                            </form>
                        </div>
                    </div>

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

                                <input type="file" multiple ref="video" v-on:change="handleVideoUpload()" style="display: none;" accept="video/mp4">
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
            duration: null,
            paid: false,
            duration_per_rep: null,
            image: [],
            video: [],
            displayProcessingModal: false,
            selected_categories: [],
            categories: [],
            sections: [
                {
                    id: 1,
                    name: 'Warm Up'
                },
                {
                    id: 2,
                    name: 'Training'
                },
                {
                    id: 3,
                    name: 'Cool Down'
                },
            ],
            selected_focuses: [],
            focuses: [],
            selected_sections: [],
            intensities: [],
            steps: [],
            selected_intensity: null
        }
    },

    methods: {
        save() {
            this.uploading = true;

            axios.post('/api/admin/exercise/library', {
                name: this.name,
                description: this.description,
                image: this.image,
                video: this.video,
                selected_categories: this.selected_categories,
                selected_sections: this.selected_sections,
                duration: this.duration,
                duration_per_rep: this.duration_per_rep,
                selected_focuses: this.selected_focuses,
                selected_intensity: this.selected_intensity,
                steps: this.steps,
                paid: this.paid
            })
            .then(response => {
                this.uploadImage(response.data.id);
                this.syncCategories(response.data.id);
            })
            .catch(error => {
                this.uploading = false;
                this.errors = error.response.data.errors;
            });
        },

        togglePaid() {
            this.paid = this.paid ? true : false;
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
            axios.post('/api/admin/exercise/library/' + resource_id + '/upload-image', formData, {
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
            axios.post('/api/admin/exercise/library/' + resource_id + '/upload-video', formData, {
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
                //window.location.reload();
            });
        },

        getCategories(){
            axios.get('/api/admin/exercise/categories').then(response => {
                this.categories = response.data.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
        },
        getFocuses(){
            axios.get('/api/admin/exercise/focuses').then(response => {
                this.focuses = response.data;
            });
        },
        getIntensities(){
            axios.get('/api/admin/exercise/intensities').then(response => {
                this.intensities = response.data;
            });
        },

        removeStep(counter){
            this.steps.splice(counter, 1);
        },
        addStep(){
            this.steps.push({
                text: ''
            });
        }
    },

    mounted(){
        this.getCategories();
        this.getFocuses();
        this.getIntensities();
    }
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
