<template>
    <div>
        <PageHeader title="Class Library" subtitle="On Demand" icon="video_library">
            <template>
                <button @click="displayUploadModal = true" class="button">
                    Upload Class
                    <i class="material-icons">add_circle</i>
                </button>
            </template>
        </PageHeader>

        <section class="tab tab--no-bottom">
            <ul>
                <li class="active"><a>Library</a></li>
                <li><router-link :to="{ name: 'OnDemandCategories' }">Categories</router-link></li>
            </ul>
        </section>

        <section>
            <div v-if="this.classes.length > 0" class="page-content page-content--small-top">
                <div class="table-list table-list--top">
                    <div class="table-list__header">
                        <h3>{{ pagination.from + ' - ' + pagination.to }} of {{ pagination.total }} Classes</h3>

                        <div class="table-list__header-pagination">
                            <span :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === 1}" @click="change_page('previous')">navigate_before</span>
                            <span :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === pagination.last_page}" @click="change_page('next')">navigate_next</span>
                        </div>
                    </div>

                    <div class="table-list__scroll">
                        <table class="table-list__table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Categories</th>
                                    <th>Series Order</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(item, index) in classes" :key="index">
                                    <td><router-link :to="'/admin/on-demand/library/' + item.id">{{ item.name }}</router-link></td>
                                    <td>{{ item.excerpt }}</td>
                                    <td><span style="display: block;" v-for="(item, index) in item.categories">{{ item.name }}</span></td>
                                    <td>{{ item.order }}</td>
                                    <td>
                                        <span v-if="item.processed" class="dot dot--green"></span>
                                        <i v-else class="fas fa-circle-notch fa-spin"></i>&nbsp;
                                        {{ item.processed ? 'Live' : 'Processing' }}
                                    </td>
                                    <td>{{ item.created_human }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <section class="forbidden forbidden--full" v-else-if="!this.loading && this.classes.length == 0">
                <div>
                    <img src="/images/illustrations/video-streaming.svg">
                    <h2>No Videos</h2>
                    <p>Why not upload your first one?</p>
                    <br><br>
                    <button @click="displayUploadModal = true" class="button">Upload Video <i class="fas fa-plus"></i></button>
                </div>
            </section>
        </section>

        <upload-video-modal v-on:cancel="displayUploadModal = false" v-on:complete="displayUploadModal = false" :class="displayUploadModal ? 'modal modal--active' : 'modal'" />
    </div>
</template>

<script>
import axios from 'axios';
import UploadVideoModal from './UploadVideoModal.vue';
import FilterableDataTable from '../layout/FilterableDataTable.vue'
import PageHeader from '../layout/PageHeader.vue'

export default {
    components: {
        UploadVideoModal,
        FilterableDataTable,
        PageHeader
    },

    data() {
        return {
            loading: true,
            classes: [],
            displayUploadModal: false,
            classListHeadings: {
                name: 'Name',
                excerpt: 'Description',
                categories: 'Categories',
                processed: 'Status',
                created_human: 'Created'
            }
        }
    },

    mounted() {
        this.loadClasses();
    },

    methods: {
        scrollToTop() {
            document.getElementById('app').scrollIntoView();
        },

        loadClasses(page) {
            console.log('Loading Classes...');

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/on-demand/library?page=' + page).then(response => {
                this.classes = response.data.data;
                this.pagination = response.data;
            })
            .catch(error => {
                if(error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            })
            .finally(() => this.loading = false);
        },

        change_page(type) {
            this.loading = true;

            if (type === 'previous') {
                this.loadClasses((this.pagination.current_page - 1));
            } else {
                if (this.pagination.current_page !== this.pagination.last_page) {
                    this.loadClasses((this.pagination.current_page + 1));
                }
            }

            this.scrollToTop();
        },

        deleteOnDemandClass(item) {
            alert('In Development...');
        }
    }
}
</script>
