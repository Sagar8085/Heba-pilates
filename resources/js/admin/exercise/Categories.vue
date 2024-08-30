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

                    <h2 class="page-header__sub">Categories</h2>
                </div>

                <div class="page-header__col">
                    <button @click="displayCreateModal = true" class="button">Create New Category <i class="material-icons">add_circle</i></button>
                </div>
            </div>
        </section>

        <section class="tab tab--no-bottom">
            <ul>
                <li><router-link :to="{ name: 'ExerciseLibrary' }">Library</router-link></li>
                <li class="active"><a>Categories</a></li>
            </ul>
        </section>

        <section>
            <div v-if="this.categories.length > 0" class="page-content page-content--small-top">
                <filterable-data-table
                    :cols="categoryListHeadings"
                    :rows="categories"
                    :pagination="pagination"
                    title="Categories"
                    searchPlaceholder="Search Categories"
                    v-on:nextPage="loadCategories((pagination.current_page + 1))"
                    v-on:previousPage="loadCategories((pagination.current_page - 1))">

                    <template v-slot:cell-name="{ item, cell }">
                        <router-link :to="'/admin/exercise/library/' + item.id">{{ cell }}</router-link>
                    </template>

                    <template v-slot:cell-status="{ item, cell }">
                        <i class="fas fa-circle-notch fa-spin" v-if="!item.processed"></i>&nbsp;
                        <span :class="item.processed ? 'dot dot--green' : ''"></span>
                        {{item.processed ? 'Live' : 'Processing'}}
                    </template>

                    <template v-slot:cell-actions="{ item }">
                        <button class="button button--icon button--transparent button--red" @click="deleteCategory(item)">
                            <i class="material-icons">delete</i>
                        </button>
                    </template>


                </filterable-data-table>

            </div>

            <section class="forbidden forbidden--full" v-else-if="!this.loading && this.categories.length == 0">
                <div>
                    <img src="/images/illustrations/video-streaming.svg">
                    <h2>No Categories</h2>
                    <p>Why not create your first one?</p>
                    <br><br>
                    <button @click="displayCreateModal = true" class="button">Create Category <i class="fas fa-plus"></i></button>
                </div>
            </section>
        </section>

        <create-category-modal v-on:cancel="displayCreateModal = false" v-on:complete="displayCreateModal = false" :class="displayCreateModal ? 'modal modal--active' : 'modal'" />
        <delete-modal
            v-on:close="displayDeleteModal = false"
            v-on:complete="displayDeleteModal = false; refresh()"
            element="Category"
            :url="'/api/admin/exercise/category/'+this.category.id+'/delete'"
            :class="displayDeleteModal ? 'modal modal--active' : 'modal'"
        />
    </div>
</template>

<script>
import axios from 'axios';
import CreateCategoryModal from './CreateCategoryModal.vue';
import FilterableDataTable from '../layout/FilterableDataTable.vue'
import DeleteModal from '../../components/DeleteModal.vue'

export default {
    components: {
        CreateCategoryModal,
        FilterableDataTable,
        DeleteModal
    },

    data() {
        return {
            loading: true,
            categories: [],
            displayCreateModal: false,
            displayDeleteModal: false,
            category: {},
            hasCategories:true,
            pageContent: 'categories',
            categoryListHeadings: {
                name: 'Name',
                excerpt: 'Description',
                created_human: 'Created',
                actions: ''
            }
        }
    },

    mounted() {
        this.loadCategories();
    },

    methods: {
        loadCategories(page) {
            console.log('Loading Categories...');

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/exercise/categories?page='+page).then(response => {
                this.categories = response.data.data;
                this.pagination = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },
        refresh(){
            this.loadCategories((this.pagination.current_page));
        },
        deleteCategory(item, index) {
            this.category = item;
            this.displayDeleteModal = true;
        }
    }
}
</script>
