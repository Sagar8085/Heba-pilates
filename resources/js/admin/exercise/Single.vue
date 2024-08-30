<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--member">
                            <i class="fas fa-podcast"></i>
                        </div>
                        Exercises
                    </h1>

                    <h2 class="page-header__sub">Edit Exercise</h2>
                </div>
            </div>
        </section>



        <section class="back">
            <a @click="$router.go(-1)" class="navigation__back"><img src="/images/icons/backblue.png" alt=""> &nbsp; Back</a>
        </section>

        <section class="page-content">

            <div class="form-element">
                <span class="form-element__label">
                    * Name of Video / Class
                    <span v-if="this.errors['name']">{{ this.errors['name'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <input type="text" required v-model="exercise.name">
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                    * Description
                    <span v-if="this.errors['description']">{{ this.errors['description'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <input type="text" required v-model="exercise.description">
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                    * Paid only (don't include in webportal create workout, checked = paid only)
                    <span v-if="this.errors['paid']">{{ this.errors['paid'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <input id="paid_checkbox" class="checkbox" type="checkbox" @click="togglePaid()" v-model="exercise.paid">
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                    * Default Duration (seconds)
                    <span v-if="this.errors['duration']">{{ this.errors['duration'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <input type="number" required v-model="exercise.duration">
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                    * Duration Per Rep (How long does a single rep take to completion in seconds)
                    <span v-if="this.errors['duration_per_rep']">{{ this.errors['duration_per_rep'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <input type="number" required v-model="exercise.duration_per_rep">
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                     Intensity*
                    <span v-if="this.errors['intensities']">{{ this.errors['intensities'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <div>
                        <multiselect v-model="exercise.intensity" deselect-label="Can't remove this value" track-by="id" label="intensity" placeholder="Select one" :options="intensities" :searchable="false" :allow-empty="false"></multiselect>
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
                      <multiselect v-model="exercise.focus" :options="focuses" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose which focus" label="name" track-by="id" :preselect-first="false"></multiselect>
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
                      <multiselect v-model="exercise.sections" :options="sections" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose which sections" label="name" track-by="id" :preselect-first="false"></multiselect>
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
                      <multiselect v-model="exercise.categories" :options="categories" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose some categories" label="name" track-by="slug" :preselect-first="false"></multiselect>
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
                            <tr  v-for="(step, counter) in exercise.steps" v-bind:key="counter">
                                <td class="no-border textarea-input"><textarea class="deletable" type="text" required v-model="step.text"></textarea></td>
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
                    <form enctype="multipart/form-data" ref="form">
                        <div class="chat__files" @click="openVideoFileBrowser()">
                            <span v-if="this.video.length == 0">Click to Upload Video <img src="/icons/utility/attach_60.png"></span>
                            <span v-else>{{this.video[0].name}}</span>
                        </div>

                        <input type="file" multiple ref="video"  v-on:change="handleVideoUpload()" style="display: none;" accept="video/mp4">
                    </form>
                </div>
            </div>
            <div class="form-element form-element--footer">
                <div class="form-element--footer__left">
                    <!-- <button @click="showDeleteModal = true" class="button button--red-outline" v-if="ondemand.status === 'published'">Unpublish</button>
                    <button @click="showPublishModal = true" class="button button--green" v-if="ondemand.status !== 'published'">Publish</button> -->
                </div>

                <div class="form-element--footer__right">
                    <button class="button" v-if="!this.uploading" @click="update()">Save</button>
                    <button class="button" v-else>Saving&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
                </div>
            </div>
        </section>

        <div v-if="displayProcessingModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">File Processing</h2>

                <p>Your file has been queued for processing, this is mandatory so we can reduce the file size and strip un-unnecessary bites from your file to make video load times faster for customers and reduce their bandwidth.</p>

                <div class="modal-alert__buttons">
                    <button class="button button--green" @click="displayProcessingModal = false; showSavedModal = true">Okay</button>
                </div>
            </div>
        </div>

        <div v-if="showSavedModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Save Successful</h2>

                <p>This exercise has been updated and updates are now live for users.</p>

                <div class="modal-alert__buttons">
                    <button class="button button--green" @click="showSavedModal = false">Okay</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            showDeleteModal: false,
            showPublishModal: false,
            showSavedModal: false,
            showCustomisedModal: false,
            displayProcessingModal: false,
            uploading: false,

            loading: true,
            exercise: {},

            errors: [],
            categories: [],

            warmup_exercises: [],
            training_exercises: [],
            cooldown_exercises: [],

            customOptions: [
                '',
                'setsReps',
                'duration'
            ],

            image: [],
            video: [],

            uploadPercentage: 0,
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
            focuses: [],
            intensities: [],
            steps: [],
        }
    },

    mounted() {
        this.loadExercise();
        this.getCategories();
        this.getFocuses();
        this.getIntensities();
    },

    methods: {
        /*
         * Load the single resource.
         * @param {none}
         */
        loadExercise() {
            axios.get('/api/admin/exercise/library/' + this.$route.params.id).then(response => {
                this.exercise = response.data;
                console.log(this.exercise)
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        /*
         * Load all the categories available for selection.
         * @param {none}
         */
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
             this.exercise.steps.splice(counter, 1);
         },

         addStep(){
             this.exercise.steps.push({
                 text: ''
             });
         },

         togglePaid() {
             this.exercise.paid = this.exercise.paid ? 1 : 0;
         },

        /*
         * Update the main podcast resource.
         * @param {status} string
         */
        update(status) {
            this.uploading = true;

            axios.post('/api/admin/exercise/'+this.exercise.id+'/update', {
                exercise: this.exercise,
            }).then(response => {
                if (this.image.length > 0) {
                    this.uploadImage(this.exercise.id);
                } else {
                    if(this.video.length > 0){
                        this.uploadVideo(this.exercise.id);
                    } else {
                        this.showSavedModal = true;
                        this.uploading = false;
                    }
                }
            }).catch(error => {
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

            console.log(formData, resource_id);

            /*
             * Make the request to the POST /multiple-files URL
             */
            axios.post('/api/admin/exercise/library/' + resource_id + '/upload-image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                if(this.video.length > 0){
                    this.uploadVideo(resource_id);
                } else {
                    this.uploading = false;
                    this.image = [];
                    this.showSavedModal = true;
                }
            })
            .catch(function(error){
                console.log('FAILURE!!');
                console.log(error);
            });
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
                this.video = [];
                this.image = [];
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

        openVideoFileBrowser() {
            this.$refs.video.click();
        },

        handleVideoUpload() {
            this.video = this.$refs.video.files;
            console.log(this.video);
        },

        openImageFileBrowser() {
            this.$refs.image.click();
        },

        handleImageUpload() {
            this.image = this.$refs.image.files;
            console.log(this.image);
        },
    }
}
</script>
