<template>
    <header class="header" v-if="isAuthed">
        <div class="header__items wrapper">
            <div class="header__burger">
                <router-link to="/tablet" class="header__burger__home">
                    <img src="/images/logos/heba-logo.png" alt="Hebe Pilates" title="Hebe Pilates">
                </router-link>
                <div :class="{ 'burger2 menu': true, 'open': burgerOpen }" @click="toggleBurgerMenu">
                    <div class="icon"></div>
                </div>
            </div>

            <transition :name="desktopNav ? '' : 'hamburger-menu'" mode="out-in">
                <nav v-if="burgerOpen || desktopNav" class="header__nav">
                    <ul :class="{ header__navigation: true, 'active': burgerOpen }">
                        <router-link class="header__burger__responsive-home header__burger__home" to="/tablet">
                            <img src="/images/logos/heba-logo.png" alt="Hebe Pilates" title="Hebe Pilates">
                        </router-link>
                        <template v-if="isAuthed">
                            <!-- <li :class="{ active: this.$route.path.includes('/tablet/on-demand') }"><router-link :to="'/tablet/on-demand'">On Demand</router-link></li>
                            <li :class="{ active: this.$route.path.includes('/tablet/live-classes') }"><router-link :to="'/tablet/live-classes'"><div class="live-icon live-icon--right"><span class="animate-flicker"></span></div> Live Classes</router-link></li> -->
                        </template>
                    </ul>

                    <ul :class="{ 'header__navigation header__navigation--right': true, 'active': burgerOpen }">
                        <li class="header__navigation__item--large active"><a href="javascript: location.reload()">Reload</a></li>

                        <template v-if="isAuthed && !this.$route.path.includes('welcome')">
                            <li class="header__navigation__item--large active"><a @click="endTabletSession()">End Session</a></li>
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
import HeaderDropdown from '../../components/HeaderDropdown.vue';
import SearchInput from '../../components/SearchInput.vue';
import EndsTabletSession from '../../mixins/endsTabletSession';

export default {
    mixins: [EndsTabletSession],
    components: { HeaderDropdown, SearchInput },
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
                    title: 'Live Class Bookings'
                },
                {
                    icon: 'account_circle',
                    link: '/myaccount',
                    title: 'Account Settings'
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

            localStorage.removeItem('tablet-token');
            sessionStorage.removeItem('tablet-token');
            window.location.href = '/tablet';
        },
        onResize (window) {
            const maxNavBreakpoint = 767;

            this.desktopNav = window.innerWidth > maxNavBreakpoint;
        },
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
