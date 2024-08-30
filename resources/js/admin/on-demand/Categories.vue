<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">video_library</i>
                        </div>
                        On Demand Classes
                    </h1>

                    <h2 class="page-header__sub">Categories</h2>
                </div>

                <div class="page-header__col">
                    <router-link class="button" :to="{ name: 'OnDemandOrderCategories'}"> 
                       
                           Update Category Order
                      
                    </router-link>
                    <button @click="displayCreateModal = true" class="button">
                        Create New On Demand Category
                        <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <section class="tab tab--no-bottom">
            <ul>
                <li><router-link :to="{ name: 'OnDemandLibrary' }">Library</router-link></li>
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
                    searchPlaceholder="Search On Demand Categories"
                    @previousPage="loadCategories"
                    @nextPage="loadCategories"
                >
                    <template v-slot:cell-name="{ item, cell }">
                        <router-link :to="{ name: 'OnDemandSingleCategory', params: { id: item.id }}">
                            {{ cell }}
                        </router-link>
                    </template>

                    <template v-slot:cell-description="{ cell }">
                        <div class="data-table__table__cell--wrap">
                            {{ cell }}
                        </div>
                    </template>

                    <template v-slot:cell-actions="{ item }">
                        <!-- <button class="button button--icon button--red button--transparent" @click="deleteCategory(item)">
                            <i class="material-icons">delete</i>
                        </button> -->
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
    </div>
</template>

<script>
import axios from 'axios';
import CreateCategoryModal from './CreateCategoryModal.vue';
import FilterableDataTable from '../layout/FilterableDataTable.vue'

export default {
    components: {
        CreateCategoryModal,
        FilterableDataTable
    },

    data() {
        return {
            loading: true,
            categories: [],
            pagination: {},
            displayCreateModal: false,
            categoryListHeadings: {
                name: 'Name',
                description: 'Description',
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
            if (page === undefined) {
                page = 1;
            }

            axios.get(`/api/admin/on-demand/categories?page=${page}`).then(response => {
                this.categories = response.data.data;
                this.pagination = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        deleteCategory(item) {
            alert('in development');
        }
    }
}
</script>
