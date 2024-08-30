<template>
    <div>
        <section class="page-header page-header--no-bottom" v-if="this.admins.length > 0">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">admin_panel_settings</i>
                        </div>
                        Admins
                    </h1>

                    <h2 class="page-header__sub">All Administrators</h2>
                </div>

                <div class="page-header__col" v-if="this.authUser.privileges.includes('admin-create')">
                    <!-- <button @click="displayUpgradeModal = true" class="button">
                        Upgrade to Admin
                        <i class="material-icons">add_circle</i>
                    </button> -->
                    <button @click="displayCreateModal = true" class="button">
                        Create Admin
                        <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <section>
            <div v-if="admins.length > 0" class="page-content page-content--small-top">
                <div class="table-list table-list--top">
                    <div class="table-list__header">
                        <h3>{{ admins.length }} Administrators</h3>
                    </div>

                    <div class="table-list__scroll">
                        <table class="table-list__table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Access Type</th>
                                    <th>Email Address</th>
                                    <th>Phone Number</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(item, index) in admins" :key="index">
                                    <!-- <td class="has-avatar"> -->
                                    <td>
                                        <!-- <profile-picture :image="item.avatar" :firstName="item.first_name" :lastName="item.last_name" /> -->
                                        <router-link :to="'/admin/admins/' + item.id">{{ item.name }}</router-link>
                                    </td>
                                    <td>{{ item.superadmin ? 'Superadmin' : 'Admin' }}</td>
                                    <td>{{ item.email }}</td>
                                    <td>{{ item.phone_number }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <section class="forbidden forbidden--full" v-if="!this.loading && this.admins.length == 0">
                <div>
                    <img src="/images/illustrations/video-streaming.svg">
                    <h2>No Admins</h2>
                    <p>Why not create your first one?</p>
                    <br><br>
                    <button @click="displayCreateModal = true" class="button">Create Admin <i class="fas fa-plus"></i></button>
                </div>
            </section>
        </section>

        <create-admin-modal v-on:cancel="displayCreateModal = false" v-on:complete="displayCreateModal = false; loadMembers()" :class="displayCreateModal ? 'modal modal--active' : 'modal'" />
        <upgrade-to-admin-modal v-on:cancel="displayUpgradeModal = false" v-on:complete="displayUpgradeModal = false; loadMembers()" :class="displayUpgradeModal ? 'modal modal--active' : 'modal'" />
    </div>
</template>

<script>
import axios from 'axios';
import CreateAdminModal from './CreateAdminModal.vue';
import UpgradeToAdminModal from './UpgradeToAdminModal.vue';
import FilterableDataTable from '../layout/FilterableDataTable.vue';
import ProfilePicture from '../../components/ProfilePicture.vue';

export default {
    props: {
        authUser: Object
    },

    components: {
        CreateAdminModal,
        FilterableDataTable,
        ProfilePicture,
        UpgradeToAdminModal
    },

    data() {
        return {
            loading: true,
            displayCreateModal: false,
            displayUpgradeModal: false,
            admins: [],
            pagination: {},
            adminsListHeadings: {
                image: '',
                name: 'Name',
                type: 'Type',
                email: 'Email',
                phone_number: 'Phone Number',
                created_at: 'Registered',
            }
        }
    },

    mounted() {
        this.loadMembers();
    },

    methods: {
        scrollToTop() {
            document.getElementById('app').scrollIntoView();
        },

        loadMembers(page) {
            console.log('Loading Members...');

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/admins?page=' + page).then(response => {
                this.admins = response.data.data;
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
                this.fetchClasses((this.pagination.current_page - 1));
            } else {
                this.fetchClasses((this.pagination.current_page + 1));
            }

            this.scrollToTop();
        },

        editAdmin (admin) {

        }
    }
}
</script>
