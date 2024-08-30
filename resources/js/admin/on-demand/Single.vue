<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">video_library</i>
                        </div>
                        Classes On-Demand
                    </h1>

                    <h2 class="page-header__sub">Edit Class</h2>
                </div>
            </div>
        </section>

        <section class="back">
            <router-link to="/admin/on-demand/library">
                <img src="/images/icons/backblue.png" alt="Back"> &nbsp; Back
            </router-link>
        </section>

        <section class="page-content">
            <div class="info info--bottom" v-if="ondemand.processing">
                <p><strong><i class="fas fa-info-circle"></i> How do I change my videos status from Processing?</strong></p>
                <p>The status of your video is automatically changed to <strong>Published</strong> once we have finished transcoding your video into a streamable format, this cannot be done manually. Depending on the size and length of your video, it can take up to 1 hour to finish processing.</p>
            </div>

            <div class="row">
                <div class="six columns">
                    <div class="form-element">
                        <span class="form-element__label">
                            * Name of class
                            <span v-if="this.errors['name']">{{ this.errors['name'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <input type="text" required v-model="ondemand.name">
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
                            <input type="text" required v-model="ondemand.duration">
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
                    <textarea type="text" required v-model="ondemand.description" rows="5"></textarea>
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                     Category
                    <span v-if="this.errors['description']">{{ this.errors['description'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <div>

                        <multiselect v-model="ondemand.categories" :options="categories" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose some categories" label="name" track-by="slug" :preselect-first="false"></multiselect>
                    </div>
                </div>
            </div>
            <div class="form-element">
                <span class="form-element__label">
                     Published
                    <span v-if="this.errors['published']">{{ this.errors['published'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <div>
                        <select v-model="ondemand.published">
                            <option value="" selected disabled>-- Select ---</option>
                            <option value="1">Published</option>
                            <option value="0">Hidden</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                     Modify Existing Tags
                    <span v-if="this.errors['selectedTags']">{{ this.errors['selectedTags'][0] }}</span>
                </span>
                <small class="form-element__hint extra-small">Add and remove tags that have been previously used.</small>

                <div class="form-element__control">
                    <div class="row">
                        <div class="ten columns">
                            <multiselect v-if="allTags.length > 0" v-model="selectedTags" :options="allTags" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose some tags" label="name" track-by="slug" :preselect-first="false"></multiselect>
                        </div>

                        <div class="two columns">
                            <button class="button button--full" @click="saveTags()">Save</button>
                        </div>
                    </div>
                </div>

                <span class="form-element__label">
                     Add New Tag
                    <span v-if="this.errors['newTag']">{{ this.errors['newTag'][0] }}</span>
                </span>
                <small class="form-element__hint extra-small">Add and assign a completely new tag that has never been used before.</small>

                <div class="form-element__control">
                    <div class="row">
                        <div class="ten columns">
                            <input placeholder="Total Body" v-model="newTag">
                        </div>

                        <div class="two columns">
                            <button class="button button--full" @click="createTag()">Create</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="three columns">
                        <span class="form-element__label">
                            Recording
                            <span v-if="this.errors['video']">{{ this.errors['video'][0] }}</span>
                        </span>

                        <div class="form-element__control">
                            <form @submit="uploadFiles()" enctype="multipart/form-data" ref="form">
                                <div class="chat__files chat__files--full" @click="openVideoFileBrowser()">
                                    <span v-if="this.video.length == 0">Click to Replace Video <img src="/icons/utility/attach_60.png"></span>
                                    <span v-else>{{this.video[0].name}}</span>
                                </div>

                                <input type="file" multiple ref="video" id="video" v-on:change="handleVideoUpload()" style="display: none;" accept="video/mp4">
                            </form>
                        </div>
                    </div>

                    <div class="three columns">
                        <span class="form-element__label">
                            Image
                            <span v-if="this.errors['image']">{{ this.errors['image'][0] }}</span>
                        </span>

                        <div class="form-element__control">
                            <form enctype="multipart/form-data" ref="form">
                                <div class="chat__files chat__files--full" @click="openImageFileBrowser()">
                                    <span v-if="this.image.length == 0">Click to Replace Image <img src="/icons/utility/attach_60.png"></span>
                                    <span v-else>{{this.image[0].name}}</span>
                                </div>

                                <input type="file" multiple ref="image" id="image" v-on:change="handleImageUpload()" style="display: none;" accept="image/jpg,image/jpeg,image/png,image/webp">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-element form-element--footer">
                <div class="form-element--footer__left">
                    <button @click="showDeleteModal = true" class="button button--red-outline">Delete</button>
                </div>

                <div class="form-element--footer__right">
                    <button v-if="!this.uploading" class="button" @click="update()">Save</button>
                    <button v-else class="button">{{this.uploadPercentage}}%&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
                </div>
            </div>
        </section>

        <div v-if="showDeleteModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">{{ ondemand.name }}</h2>

                <p>Are you sure you wish to delete this class video?</p>

                <div class="modal-alert__buttons">
                    <button class="button button--outline" @click="showDeleteModal = false">Cancel</button>
                    <button class="button button--with-icon button--red" @click="deleteClass">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showSavedModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Save Successful</h2>

                <p>This class has been updated and updates are now live for users.</p>

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

            loading: true,
            ondemand: {
                category_id: ''
            },

            errors: [],
            categories: [],

            image: [],
            video: [],
            uploading: false,
            uploadPercentage: 0,

            allTags: [],
            selectedTags: [],
            newTag: '',
        }
    },

    mounted() {
        this.loadPodcast();
        this.getCategories();
        this.loadTags();
    },

    methods: {
        /*
         * Load the single resource.
         * @param {none}
         */
        loadPodcast() {
            axios.get('/api/admin/on-demand/library/' + this.$route.params.id).then(response => {
                this.ondemand = response.data;
            })
            .catch(error => {
                if(error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            })
            .finally(() => this.loading = false);
        },

        /*
         * Delete the class resource.
         * @param {none}
         */
        deleteClass() {
            axios.delete('/api/admin/on-demand/library/' + this.$route.params.id).then(response => {
                this.$router.push('/admin/on-demand/library');
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
        getCategories() {
            axios.get('/api/admin/on-demand/categories').then(response => {
                this.categories = response.data.data;
                console.log('categories ', this.categories);
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
        },
        openImageFileBrowser() {
               this.$refs.image.click();
        },
        handleImageUpload() {
            this.image = this.$refs.image.files;
            console.log(this.image);
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
            axios.post('/api/admin/on-demand/library/' + this.$route.params.id + '/upload-image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                console.log('done');
            })
            .catch(function(error){
                console.log('FAILURE!!');
                console.log(error);
            });
        },

        /*
         * Update the main podcast resource.
         * @param {status} string
         */
        update(status) {
            delete this.errors['image']
            delete this.errors['video']

            axios.patch('/api/admin/on-demand/library/' + this.$route.params.id, {
                name: this.ondemand.name,
                description: this.ondemand.description,
                categories: this.ondemand.categories,
                duration: this.ondemand.duration,
                published: this.ondemand.published
            }).then(response => {
                console.log(response);
                this.saveTags();
                this.uploadImage();
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
        },

        loadTags() {
            axios.get('/api/admin/on-demand/tags').then(response => {
                this.allTags = response.data.data;
            })

            axios.get('/api/admin/on-demand/tags/' + this.$route.params.id).then(response => {
                this.selectedTags = response.data.data;
            })
        },

        saveTags() {
            axios.patch('/api/admin/on-demand/tags/' + this.$route.params.id, {
                tags: this.selectedTags
            })
        },

        createTag() {
            axios.post('/api/admin/on-demand/tags', {
                tag: this.newTag,
                ondemand_id: this.$route.params.id,
            }).then(response => {
                this.allTags.push(response.data.tag);
                this.selectedTags.push(response.data.tag);
                this.newTag = '';
            })
        },

        openImageFileBrowser() {
            this.$refs.image.click();
        },

        handleImageUpload() {
            this.image = this.$refs.image.files;
        },

        uploadImage() {
            if (this.image.length <= 0) {
                this.uploadVideo();
                return;
            }

            this.uploading = true
            const formData = new FormData();

            this.image.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
            });

            axios.post('/api/admin/on-demand/library/' + this.$route.params.id + '/upload-image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.uploadVideo();
            }).catch(error => {
                this.errors['image'] = ['Failed to upload image'];
            }).finally(() => {
                this.uploading = false;
            });
        },

        openVideoFileBrowser() {
            this.$refs.video.click();
        },

        handleVideoUpload() {
            this.video = this.$refs.video.files;
        },

        uploadVideo() {
            if (this.video.length <= 0) {
                this.showSavedModal = true;
                this.resetUploads();
                return;
            }

            this.uploading = true;
            const formData = new FormData();

            this.video.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
            });

            axios.post('/api/admin/on-demand/library/' + this.$route.params.id + '/upload-video', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: progressEvent => {
                    this.uploadPercentage = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                },
            }).then(response => {
                this.showSavedModal = true;
                this.resetUploads();
            }).catch(error => {
                this.errors['video'] = ['Failed to upload video'];
            }).finally(() => {
                this.uploading = false;
            });
        },

        resetUploads() {
            this.image = [];
            this.video = [];
            this.uploadPercentage = 0;
        },
    }
}
</script>
