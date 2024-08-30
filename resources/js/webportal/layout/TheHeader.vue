<template>
    <header class="header" v-if="!this.$route.path.includes('/onboard')">
        <div class="header__items wrapper">
            <div class="header__burger">
                <router-link to="/" class="header__burger__home">
                    <img src="/images/logos/heba-logo.png" alt="Heba Pilates" title="Heba Pilates">
                </router-link>
                <div :class="{ 'burger2 menu': true, 'open': burgerOpen }" @click="toggleBurgerMenu">
                    <div class="icon"></div>
                </div>
            </div>

            <transition :name="desktopNav ? '' : 'hamburger-menu'" mode="out-in">
                <nav v-if="burgerOpen || desktopNav" class="header__nav">
                    <ul :class="{ header__navigation: true, 'active': burgerOpen }">
                        <router-link class="header__burger__responsive-home header__burger__home" to="/">
                            <img src="/images/logos/heba-logo.png" alt="Heba Pilates" title="Heba Pilates">
                        </router-link>
                        <template v-if="isAuthed">
                            <li v-if="authUser.role_id === 1 || authUser.role_id === 2" :class="{ active: this.$route.path.includes('/on-demand') }"><router-link :to="'/on-demand'">On Demand</router-link></li>
                            <!-- <li :class="{ active: this.$route.path.includes('/live-classes') }"><router-link :to="'/live-classes'"><div class="live-icon live-icon--right"><span class="animate-flicker"></span></div> Live Classes</router-link></li> -->
                            <li :class="{ active: this.$route.path.includes('/studio') }"><router-link :to="'/studio'">Reserve Studio Time</router-link></li>
                            <li :class="{ active: this.$route.path.includes('/help') }"><router-link :to="'/help'">Help Centre</router-link></li>
                        </template>
                    </ul>

                    <ul :class="{ 'header__navigation header__navigation--right': true, 'active': burgerOpen }">
                        <template v-if="isAuthed && !this.$route.path.includes('welcome')">
                            <!-- <li class="header__search">
                                <router-link to="/search">
                                    <i class="material-icons">search</i>
                                    <span class="header__search__text">Search</span>
                                </router-link>
                                <search-input
                                    placeholder="Search On Demand, Live Classes, Categories, Instructors"
                                    isHeader
                                    @search="goToSearch" />
                            </li> -->
                            <header-notifications :notifications="authUser.latest_notifications" ref="notifications" @show="onNotificationsShow" />
                            <header-dropdown ref="accountDropdown" :dropdownItems="accountDropdownItems" @show="onAccountShow" @click="logout">
                                <a class="header__account">
                                    <i class="material-icons">account_circle</i>
                                    <span class="header__account__text">Account</span>
                                </a>
                            </header-dropdown>
                        </template>
                        <template v-else>
                            <li class="header__navigation__item--large"><router-link to="/login">Log in</router-link></li>
                            <li class="header__navigation__item--large active"><router-link to="/signup">Sign up</router-link></li>
                        </template>
                    </ul>
                </nav>
            </transition>
        </div>
    </header>
</template>

<script>
import HeaderNotifications from '../notifications/HeaderNotifications.vue';
import HeaderDropdown from '../../components/HeaderDropdown.vue';
import SearchInput from '../../components/SearchInput.vue';
export default {
    components: { HeaderNotifications, HeaderDropdown, SearchInput },
    props: {
        isAuthed: Boolean,
        authUser: Object,
        isBurgerOpen: Boolean
    },
    data () {
        return {
            desktopNav: true,
            search: '',
            accountDropdownItems: [
                {
                    icon: 'event',
                    link: '/bookings',
                    title: 'Bookings'
                },
                {
                    icon: 'account_circle',
                    link: '/myaccount',
                    title: 'Account Settings'
                },
                {
                    icon: 'qr_code',
                    link: '/myaccount/qr',
                    title: 'View My QR Code'
                },
                {
                    icon: 'local_atm',
                    link: '/membership',
                    title: 'Memberships and Credit'
                },
                {
                    icon: 'logout',
                    title: 'Logout'
                }
            ]
        }
    },
    computed: {
        burgerOpen: {
            get () { return this.isBurgerOpen },
            set (val) { this.$emit('update:is-burger-open', val) }
        }
    },
    methods: {
        closeDropdowns () {
            this.$refs.notifications.close();
            this.$refs.accountDropdown.close();
        },
        toggleBurgerMenu () {
            this.burgerOpen = !this.burgerOpen;
        },
        onNotificationsShow () {
            this.$refs.accountDropdown.close();
        },
        onAccountShow () {
            this.$refs.notifications.close()
        },
        goToSearch(search) {
            this.$router.push('/search?q=' + search);
        },
        /**
         * Logout a user.
         */
        logout(item) {
            if (item !== 'Logout') return;

            localStorage.removeItem('fc-usertoken');
            sessionStorage.removeItem('fc-usertoken');
            window.location.href = '/login';
        },
        onResize (window) {
            const maxNavBreakpoint = 767;

            this.desktopNav = window.innerWidth > maxNavBreakpoint;
        }
    },

    beforeMount () {
        window.addEventListener('resize', e => this.onResize(e.target))
    },
    mounted () {
        this.onResize(window);
    },
    beforeDestroy () {
        window.removeEventListener('resize', e => this.onResize(e.target))
    }
}
</script>
