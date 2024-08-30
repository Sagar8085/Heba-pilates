<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">directions_run</i>
                        </div>
                        Exercises
                    </h1>

                    <h2 class="page-header__sub">Video Library</h2>
                </div>

                <div class="page-header__col">
                    <button @click="displayUploadModal = true" class="button">Create New Exercise <i class="material-icons">add_circle</i></button>
                </div>
            </div>
        </section>

        <section class="tab tab--no-bottom">
            <ul>
                <li class="active"><a>Library</a></li>
                <li><router-link :to="{ name: 'ExerciseCategories' }">Categories</router-link></li>
            </ul>
        </section>

        <section>
            <div v-if="this.videos.length > 0" class="page-content page-content--small-top">
                <filterable-data-table
                    :cols="exerciseListHeadings"
                    :rows="videos"
                    :pagination="pagination"
                    title="Exercises"
                    searchPlaceholder="Search Exercises"
                    v-on:nextPage="loadVideos((pagination.current_page + 1))"
                    v-on:previousPage="loadVideos((pagination.current_page - 1))">

                    <template v-slot:cell-name="{ item, cell }">
                        <router-link :to="'/admin/exercise/library/' + item.id">{{ cell }}</router-link>
                    </template>

                    <template v-slot:cell-status="{ item }">
                        <i class="fas fa-circle-notch fa-spin" v-if="!item.processed"></i>&nbsp;
                        <span :class="item.processed ? 'dot dot--green' : ''"></span>
                        {{item.processed ? 'Live' : 'Processing'}}
                    </template>

                    <template v-slot:cell-actions="{ item }">
                        <button class="button button--transparent button--icon button--red" @click="deleteVideo(item)">
                            <i class="material-icons">delete</i>
                        </button>
                    </template>


                </filterable-data-table>

            </div>

            <section class="forbidden forbidden--full" v-else-if="!this.loading && this.videos.length == 0">
                <div>
                    <img src="/images/illustrations/video-streaming.svg">
                    <h2>No Exercises</h2>
                    <p>Why not create your first one?</p>
                    <br><br>
                    <button @click="displayUploadModal = true" class="button">Create Exercise <i class="fas fa-plus"></i></button>
                </div>
            </section>
        </section>

        <upload-video-modal v-on:cancel="displayUploadModal = false" v-on:complete="displayUploadModal = false" :class="displayUploadModal ? 'modal modal--active' : 'modal'" />
        <delete-modal
            v-on:close="displayDeleteModal = false"
            v-on:complete="displayDeleteModal = false; refresh()"
            element="Exercise"
            :url="'/api/admin/exercise/'+this.exercise.id+'/delete'"
            :class="displayDeleteModal ? 'modal modal--active' : 'modal'"
        />
    </div>
</template>

<script>
import axios from 'axios';
import UploadVideoModal from './UploadVideoModal.vue';
import FilterableDataTable from '../layout/FilterableDataTable.vue'
import DeleteModal from '../../components/DeleteModal.vue'

export default {
    components: {
        UploadVideoModal,
        FilterableDataTable,
        DeleteModal
    },

    data() {
        return {
            loading: true,
            videos: [],
            displayUploadModal: false,
            displayEditModal: false,
            displayDeleteModal: false,
            hasCategories:true,
            pageContent: 'library',
            exercise: {},
            exerciseListHeadings: {
                name: 'Name',
                excerpt: 'Description',
                status: 'Status',
                created_human: 'Created',
                actions: ''
            }
        }
    },

    mounted() {
        this.loadVideos();
    },

    methods: {
        scrollToTop() {
            document.getElementById('app').scrollIntoView();
        },

        loadVideos(page) {
            console.log('Loading Videos...');

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/exercise/library?page=' + page).then(response => {
                this.videos = response.data.data;
                this.pagination = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        change_page(type) {
            this.loading = true;

            if (type === 'previous') {
                this.loadVideos((this.pagination.current_page - 1));
            } else {
                this.loadVideos((this.pagination.current_page + 1));
            }

            this.scrollToTop();
        },
        refresh(){
            this.loadVideos((this.pagination.current_page));
        },
        deleteVideo(item) {
            this.exercise = item;
            this.displayDeleteModal = true;
        },
        editVideo(item, index){
            this.exercise = this.videos[index];
            this.displayEditModal = true;
        },
    }
}
</script>
