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

                    <h2 class="page-header__sub">Edit Category</h2>
                </div>
            </div>
        </section>



        <section class="back">
            <a @click="$router.go(-1)" class="navigation__back"><img src="/images/icons/backblue.png" alt=""> &nbsp; Back</a>
        </section>

        <section class="page-content">

            <div class="form-element">
                <span class="form-element__label">
                    * Category Name
                    <span v-if="this.errors['name']">{{ this.errors['name'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <input type="text" required v-model="category.name">
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                    * Description
                    <span v-if="this.errors['description']">{{ this.errors['description'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <input type="text" required v-model="category.description">
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                    Gyms
                    <span v-if="this.errors['gyms']">{{ this.errors['gyms'][0] }}</span>
                </span>
                <div class="form-element__control">
                    <div>
                        <multiselect v-model="category.gyms" :options="gyms" :multiple="true" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Choose one or more gyms" label="name" track-by="id" :preselect-first="false"></multiselect>
                    </div>
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


            <div class="form-element form-element--footer">
                <div class="form-element--footer__left">
                    <!-- <button @click="showDeleteModal = true" class="button button--red-outline" v-if="ondemand.status === 'published'">Unpublish</button>
                    <button @click="showPublishModal = true" class="button button--green" v-if="ondemand.status !== 'published'">Publish</button> -->
                    <router-link :to="'/admin/on-demand/categories/' + category.id + '/order'" class="button button--outline">Update Class Order</router-link>
                </div>

                <div class="form-element--footer__right">
                    <button class="button" v-if="!this.uploading" @click="update()">Save</button>
                    <button class="button" v-else disabled>Saving&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
                </div>
            </div>
        </section>

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
            showSavedModal: false,
            uploading: false,

            category: {},

            errors: [],

            image: [],

            gyms: [],
            gym_ids: [],

            uploadPercentage: 0,

        }
    },

    mounted() {
        this.loadCategory();
        this.loadGyms();
    },

    methods: {
        /*
         * Load the single resource.
         * @param {none}
         */
        loadCategory() {
            axios.get('/api/admin/on-demand/categories/' + this.$route.params.id).then(response => {
                this.category = response.data;
                console.log(this.category)
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        /*
         * Load gym options for the multiselect.
         * @param {none}
         */
        loadGyms() {
            axios.get('/api/admin/gyms').then(response => {
                this.gyms = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            });
        },

        /*
         * Update the main podcast resource.
         * @param {status} string
         */
        update(status) {
            this.uploading = true;

            axios.post('/api/admin/on-demand/category/'+this.category.id+'/update', {
                category: this.category,
            }).then(response => {
                if (this.image.length > 0) {
                    this.uploadImage(this.category.id);
                } else {
                    this.showSavedModal = true;
                    this.uploading = false;
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
            axios.post('/api/admin/on-demand/category/' + resource_id + '/upload-image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.uploading = false;
                this.image = [];
                this.showSavedModal = true;
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
        },
    }
}
</script>
