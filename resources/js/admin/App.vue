<template>
    <div>
        <header class="header" v-if="isAuthed">
            <button class="header__menu-toggle button button--icon button--transparent" @click.stop="toggleSidebar">
                <i class="fas fa-bars" />
            </button>
            <div class="header__col header__col--logo">
                <router-link to="/admin">
                    <img class="header__logo" src="/images/logos/heba-logo.png" alt="Hebe Pilates" />
                </router-link>
                <a href="/" class="header__logo-link" target="_blank">View Site</a>
            </div>

            <div class="header__col header__col--right">
                <label class="header__search search-input">
                    <input v-model="search" placeholder="Search Guests" @keyup.enter="submitSearch()" />
                </label>

                <div class="header__account">
                    <router-link :to="'/admin/admins/' + user.id">
                        <profile-picture
                            size="small"
                            :image="user.avatar"
                            :firstName="user.first_name"
                            :lastName="user.last_name"
                            />
                        <p>{{ user.name }}</p>
                    </router-link>
                </div>
            </div>
        </header>

        <the-sidebar v-if="isAuthed" :show.sync="showSidebar" @logout="logout" :admin="user" />

        <keep-alive include="AdminGymSingle">
            <router-view :class="isAuthed ? 'page page--authed' : 'page'" :authUser="user"></router-view>
        </keep-alive>
    </div>
</template>

<script>
import axios from 'axios';
import TheSidebar from './layout/TheSidebar.vue';
import ProfilePicture from '../components/ProfilePicture.vue';

export default {
    components: { TheSidebar, ProfilePicture },

    data() {
        return {
            isAuthed: localStorage.getItem('fc-admin-usertoken') ? true : false,
            showSidebar: false,
            search: '',
            user: {
                privileges: []
            }
        }
    },

    watch: {
        $route (to, from){
            document.getElementById('app').scrollIntoView();
            this.loadAuth();
            this.showSidebar = false;
        }
    },

    mounted() {
        this.loadAuth();
    },

    methods: {
        toggleSidebar () {
            this.showSidebar = !this.showSidebar
        },
        /**
         * Logout a user.
         */
        logout() {
            localStorage.removeItem('fc-admin-usertoken');
            window.location.href = '/admin/login';
        },

        submitSearch() {
            this.$router.push('/admin/members?keyword=' + this.search);
        },

        loadAuth() {
            axios.get('/api/admin/me').then(response => {
                this.user = response.data;
            })
            .catch(error => {
                // console.log('ERROR');
            });
        },
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
