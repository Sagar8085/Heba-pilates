<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">admin_panel_settings</i>
                        </div>
                        Admins
                    </h1>
                    <h2 class="page-header__sub">Admin #{{this.admin.id}} - {{this.admin.name}}</h2>
                </div>

                <div class="page-header__col">
                    <button @click="showEditModal = true" class="button">Edit {{ this.admin.first_name }} <i class="material-icons">edit</i></button>
                </div>
            </div>
        </section>

        <tab-list v-model="activeTab" :tabs="tabs" />

        <div class="page-content">
            <div v-if="activeTab == 'dashboard'" class="row">
                <div class="columns six-xl four-xxl">
                    <div class="card">
                        <div class="card__title card__title--bold">
                            <!-- <img class="card__title__image" src="/images/placeholders/taylor.jpg" alt="Member Profile Picture" /> -->
                            <profile-picture
                                size="large"
                                :image="admin.avatar"
                                :firstName="admin.first_name"
                                :lastName="admin.last_name"
                                /><br>

                            <h2>{{ this.admin.name }}</h2>
                        </div>
                        <div class="card__body">
                            <div class="card__section">
                                <h3 class="card__subtitle">Profile Information</h3>
                                <dl class="description-list">
                                    <div class="description-list__item">
                                        <dt>Email Address</dt>
                                        <dd>{{ this.admin.email }}</dd>
                                    </div>

                                    <div class="description-list__item">
                                        <dt>Phone Number</dt>
                                        <dd>{{ this.admin.phone_number }}</dd>
                                    </div>

                                    <div class="description-list__item">
                                        <dt>Type</dt>
                                        <dd>{{ this.admin.superadmin ? 'Super Admin' : 'Normal Admin' }}</dd>
                                    </div>

                                    <div class="description-list__item">
                                        <dt>Registered</dt>
                                        <dd>{{ formatDate(admin.created_at) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="card__section">
                                <h3 class="card__subtitle">Quick Links</h3>
                                <button class="button button--transparent button--plain button--with-icon" @click="activeTab = 'privileges'">
                                    <i class="fas fa-bars" />
                                    Set Privileges
                                </button>
                            </div>
                            <div class="card__section">
                                <router-link class="button button--transparent button--plain button--with-icon" :to="{ path: '/admin/members/'+ this.admin.id}">
                                    <i class="fas fa-user" />
                                    View Member Profile
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="activeTab == 'privileges'" class="table__container">
                <section class="forbidden forbidden--full" v-if="this.authUser.id === this.admin.id">
                    <div>
                        <img src="/images/illustrations/questions.svg">
                        <h2>Cannot Edit Privileges</h2>
                        <p>You can't edit your own privileges!</p>
                    </div>
                </section>
                <section class="forbidden forbidden--full" v-else-if="this.admin.superadmin">
                    <div>
                        <img src="/images/illustrations/questions.svg">
                        <h2>Cannot Edit Privileges</h2>
                        <p>This user is a Super Admin, they have automatic access to all privileges.</p>
                    </div>
                </section>
                <section class="forbidden forbidden--full" v-else-if="!this.authUser.superadmin">
                    <div>
                        <img src="/images/illustrations/questions.svg">
                        <h2>Cannot Edit Privileges</h2>
                        <p>Only super admins can edit privileges.</p>
                    </div>
                </section>
                <table class="privileges-table table" v-else>
                    <thead>
                        <th class="table__heading table__heading--title">Set Privileges</th>
                        <th class="table__heading">Can View</th>
                        <th class="table__heading">Can Create</th>
                        <th class="table__heading">Can Edit</th>
                        <th class="table__heading">Can Delete</th>
                    </thead>
                    <tbody>
                        <tr v-for="(privilege, index) in privileges" :key="privilege.name">
                            <td class="table__cell">{{ privilege.name }}</td>
                            <td
                                v-for="prop in ['read', 'create', 'update', 'delete']"
                                :key="prop"
                                :class="{ 'table__cell table__cell--selectable': true, 'table__cell--disabled': privilege[prop] === 'disabled' ? true : false, 'table__cell--selected': privilege[prop] }"
                                @click="privilege[prop] === 'disabled' ? '' : togglePrivilege(prop, privilege.slug, index)">
                                <i class="material-icons thumbs-up">thumb_up</i>
                                <i class="material-icons block">block</i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <edit-admin-modal class="modal modal--active" v-if="showEditModal" :admin="admin" :authUser="authUser"
            v-on:cancel="showEditModal = false"
            v-on:complete="showEditModal = false; loadAdmin()"
            />
    </div>
</template>

<script>
import moment from 'moment'
import axios from 'axios';
import TabList from '../layout/TabList.vue';
import EditAdminModal from './EditAdminModal.vue';
import ProfilePicture from '../../components/ProfilePicture.vue';

export default {
    components: { TabList, EditAdminModal, ProfilePicture },

    props: {
        authUser: Object
    },

    data() {
        return {
            id: this.$route.params.id,
            loading: true,
            admin: {
                privileges: []
            },
            privileges: [],
            tabs: [
                { name: 'Dashboard', key: 'dashboard' },
                { name: 'Privileges', key: 'privileges' }
            ],
            activeTab: 'dashboard',

            showEditModal: false
        }
    },

    mounted() {
        this.loadAdmin();
    },

    methods: {
        formatDate (dateString) {
            return moment(dateString).format('DD/MM/YYYY')
        },
        togglePrivilege (prop, slug, itemIndex) {
            this.privileges[itemIndex][prop] = !this.privileges[itemIndex][prop];

            if (prop === 'create' || prop === 'update' || prop === 'delete') {
                if (!this.privileges[itemIndex]['read']) {
                    this.privileges[itemIndex]['read'] = true;
                }
            }

            if (prop === 'read') {
                if (!this.privileges[itemIndex]['read']) {
                    if (this.privileges[itemIndex]['create'] !== 'disabled') {
                        this.privileges[itemIndex]['create'] = false;
                    }

                    if (this.privileges[itemIndex]['update'] !== 'disabled') {
                        this.privileges[itemIndex]['update'] = false;
                    }

                    if (this.privileges[itemIndex]['delete'] !== 'disabled') {
                        this.privileges[itemIndex]['delete'] = false;
                    }
                }
            }

            axios.patch('/api/admin/admins/' + this.$route.params.id + '/privilege', {
                privilege: slug + '-' + prop
            }).then(response => {
                console.log('privilege updated!');
            });
        },
        /*
         * Fetch the single member on page load.
         * @param {none}
         */
        loadAdmin() {
            axios.get('/api/admin/admins/' + this.$route.params.id).then(response => {
                this.admin = response.data;
                this.privileges = [
                    {
                        name: 'Admins',
                        slug: 'admin',
                        create: this.admin.privileges.includes('admin-create'),
                        read: this.admin.privileges.includes('admin-read'),
                        update: this.admin.privileges.includes('admin-update'),
                        delete: this.admin.privileges.includes('admin-delete')
                    },
                    {
                        name: 'Guests',
                        slug: 'member',
                        create: this.admin.privileges.includes('member-create'),
                        read: this.admin.privileges.includes('member-read'),
                        update: this.admin.privileges.includes('member-update'),
                        delete: this.admin.privileges.includes('member-delete')
                    },
                    {
                        name: 'Billing',
                        slug: 'order',
                        create: this.admin.privileges.includes('order-create'),
                        read: this.admin.privileges.includes('order-read'),
                        update: this.admin.privileges.includes('order-update'),
                        delete: this.admin.privileges.includes('order-delete')
                    },
                    {
                        name: 'On Demand',
                        slug: 'class',
                        create: this.admin.privileges.includes('class-create'),
                        read: this.admin.privileges.includes('class-read'),
                        update: this.admin.privileges.includes('class-update'),
                        delete: this.admin.privileges.includes('class-delete')
                    },
                    {
                        name: 'Lead Management',
                        slug: 'lead',
                        create: this.admin.privileges.includes('lead-create'),
                        read: this.admin.privileges.includes('lead-read'),
                        update: this.admin.privileges.includes('lead-update'),
                        delete: 'disabled'
                    },
                    {
                        name: 'Revenue Dashboard',
                        slug: 'revenue-dashboard',
                        create: 'disabled',
                        read: this.admin.privileges.includes('revenue-dashboard-read'),
                        update: 'disabled',
                        delete: 'disabled'
                    },
                    {
                        name: 'Door Access Dashboard',
                        slug: 'door-access-dashboard',
                        create: 'disabled',
                        read: this.admin.privileges.includes('door-access-dashboard-read'),
                        update: 'disabled',
                        delete: 'disabled'
                    },
                    {
                        name: 'User Stats  Dashboard',
                        slug: 'user-stats-dashboard',
                        create: 'disabled',
                        read: this.admin.privileges.includes('user-stats-dashboard-read'),
                        update: 'disabled',
                        delete: 'disabled'
                    }
                ];
            })
            .catch(error => {
                if(error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            })
            .finally(() => this.loading = false);
        }
    }
}
</script>
