<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">video_library</i>
                        </div>
                        Live Streaming
                    </h1>

                    <h2 class="page-header__sub">Class Schedule & Library</h2>
                </div>

                <div class="page-header__col">
                    <button @click="displayCreateModal = true" class="button">
                        Schedule Live Class
                        <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <section class="tab tab--no-bottom">
            <ul>
                <li class="active"><a>Library</a></li>
                <li><router-link :to="{ name: 'LiveClassCategories' }">Categories</router-link></li>
            </ul>
        </section>

        <section>
            <div v-if="this.classes.length > 0" class="page-content page-content--small-top">
                <filterable-data-table
                    :cols="classListHeadings"
                    :rows="classes"
                    :pagination="pagination"
                    title="Live Classes"
                    searchPlaceholder="Search Live Class Library"
                    @nextPage="loadClasses"
                    @previousPage="loadClasses">
                    <template v-slot:cell-name="{ item, cell }">
                        <router-link :to="{ name: 'LiveClassSingle', params: { id: item.id }}">{{ item.category ? item.category.name : '' }}</router-link>
                    </template>
                    <template v-slot:cell-excerpt="{ item, cell }">
                        <div class="data-table__table__cell--wrap">
                            {{ item.datetime_human }}
                        </div>
                    </template>
                    <template v-slot:cell-categories="{ cell }">
                        <div class="data-table__table__cell--wrap">
                            <!-- {{ cell.map(x => x.name).join(', ') }} -->
                        </div>
                    </template>
                    <template v-slot:cell-processed="{ cell }">
                        <span v-if="cell" class="dot dot--green"></span>
                        <i v-else class="fas fa-circle-notch fa-spin"></i>&nbsp;
                        {{cell ? 'Live' : 'Processing'}}
                    </template>

                    <template v-slot:cell-actions="{ item, cell }">
                        <a :href="'/admin/live-classes/' + item.id + '/stream'" class="button" style="color: white;">Host Class</a>
                    </template>
                </filterable-data-table>
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

        <schedule-live-class-modal class="modal modal--active" v-if="displayCreateModal" @cancel="displayCreateModal = false" @complete="displayCreateModal = false; loadClasses()"></schedule-live-class-modal>
    </div>
</template>

<script>
import axios from 'axios';
import FilterableDataTable from '../layout/FilterableDataTable.vue'
import ScheduleLiveClassModal from './ScheduleLiveClassModal.vue'

export default {
    components: { FilterableDataTable, ScheduleLiveClassModal },

    data() {
        return {
            loading: true,
            classes: [],
            displayCreateModal: false,
            classListHeadings: {
                name: 'Name',
                excerpt: 'Description',
                actions: ''
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

            axios.get('/api/admin/live-classes?page=' + page).then(response => {
                this.classes = response.data.data;
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
                this.fetchClasses((this.pagination.current_page - 1));
            } else {
                this.fetchClasses((this.pagination.current_page + 1));
            }

            this.scrollToTop();
        },

        deleteOnDemandClass(item) {
            alert('In Development...');
        }
    }
}
</script>
