<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--member">
                            <i class="material-icons">shopping_cart</i>
                        </div>
                        Products
                    </h1>

                    <h2 class="page-header__sub">All Products</h2>
                </div>
                <div class="page-header__col">
                    <button
                        @click="displayCreateModal = true"
                        class="button"
                    >
                        Create Product <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <div>
            <section class="page-content">
                <div class="filters">
                    <div
                        class="filters__placeholder"
                        @click="toggleFilter"
                    >
                        <i class="fas fa-filter"></i>
                    </div>

                    <div :class="filterDropdown ? 'filters__dropdown filters__dropdown--active' : 'filters__dropdown'">
                        <div class="row">
                            <div class="twelve columns">
                                <h3>Search</h3>
                                <input type="text" class="filters__input" v-model="search">
                            </div>
                        </div>
                        <div class="row">
                            <div class="six columns">
                                <button
                                    style="margin-top: 1.5rem;"
                                    class="button button--full"
                                    @click="filterDropdown = false; getProducts(1)"
                                >
                                    Apply Filters
                                </button>
                            </div>
                            <div class="six columns">
                                <button
                                    style="margin-top: 1.5rem;"
                                    class="button button--full button--outline"
                                    @click="filterDropdown = false; search = null; getProducts(1)"
                                >
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="products.length > 0">
                    <div class="table-list table-list--top">
                        <div class="table-list__header">
                            <h3>
                                <span v-if="pagination.from">
                                    {{ pagination.from + ' - ' + pagination.to }} of
                                </span>
                                {{ pagination.total }} Products
                            </h3>
                            <div class="table-list__header-pagination">
                                <span
                                    :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === 1}"
                                    @click="getProducts(pagination.current_page - 1)"
                                >
                                    navigate_before
                                </span>
                                <span
                                    :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === pagination.last_page}"
                                    @click="getProducts(pagination.current_page + 1)"
                                >
                                    navigate_next
                                </span>
                            </div>
                        </div>
                        <div class="table-list__scroll">
                            <table class="table-list__table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Active</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(product, index) in products" :key="index">
                                    <td>{{ product.name }}</td>
                                    <td>&pound;{{ product.price }}</td>
                                    <td>{{ product.active ? 'Active' : 'Inactive' }}</td>
                                    <td class="table-list__table__actions">
                                        <button
                                            class="button"
                                            @click="productToUpdate = product; displayUpdateModal = !displayUpdateModal"
                                        >
                                            Edit
                                        </button>

                                        <delete-button
                                            :url="'/api/admin/product/' + product.id"
                                            @success="getProducts"
                                        />

                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <section class="forbidden forbidden--full" v-if="this.pagination.total == 0">
                    <div>
                        <img src="/images/illustrations/video-streaming.svg">
                        <h2>No Products Found</h2>
                        <p>We couldn't find any products matching your search criteria.</p>
                    </div>
                </section>
            </section>
        </div>

        <create-product-modal
            v-on:cancel="displayCreateModal = false"
            v-on:complete="displayCreateModal = false; getProducts()"
            :class="displayCreateModal ? 'modal modal--active' : 'modal'"
        />

        <update-product-modal
            v-if="productToUpdate"
            v-on:cancel="displayUpdateModal = false;"
            v-on:complete="displayUpdateModal = false; getProducts()"
            :class="displayUpdateModal ? 'modal modal--active' : 'modal'"
            :product="productToUpdate"
        />

    </div>
</template>

<script>
import axios from 'axios';
import CreateProductModal from "./CreateProductModal";
import UpdateProductModal from "./UpdateProductModal";
import DeleteButton from "../../components/DeleteButton";

export default {
    props: {
        authUser: Object
    },

    components: {
        DeleteButton,
        UpdateProductModal,
        CreateProductModal,
    },

    data() {
        return {
            displayCreateModal: false,
            displayUpdateModal: false,
            productToUpdate: null,
            search: null,
            filterDropdown: false,
            products: [],
            pagination: {
                total: 0,
                current_page: 1,
                last_page: 2,
            },
            links: [],
        }
    },

    mounted() {
        this.getProducts();
    },

    methods: {
        getProducts(page) {
            if (typeof page === 'undefined') {
                page = 1;
            }

            axios
                .get('/api/admin/product?page=' + page, {params: {search: this.search}})
                .then(({data}) => {
                    this.products = data.data;
                    this.pagination = data.meta;
                    this.links = data.links;
                });
        },

        toggleFilter() {
            this.filterDropdown = !this.filterDropdown;
        },
    }
}
</script>
