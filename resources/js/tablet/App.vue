<template>
    <div :class="{
        'login': $route.path === '/login', 'sign-up': $route.path === '/signup',
        'app--tablet': isAuthed ? true : false,
        'home': $route.path === '/'
        }">
        <the-header
            v-if="$route.path !== '/login'"
            ref="header"
            :isBurgerOpen.sync="burgerOpen"
            :authUser="authUser"
            :isAuthed="isAuthed" />

        <div :class="{'app-wrap': true, 'app-wrap--login': isAuthed ? false : true}">
            <router-view :authUser="this.authUser"></router-view>

            <!-- <div class="scroll-button-group" v-if="isAuthed && !$route.path.includes('/tablet/on-demand/video')">
                <div :class="{'scroll-button': true, 'scroll-button--up': true, 'scroll-button--up-disabled': this.isAtTop ? true : false}" @click="scrollUp()"><i class="fas fa-chevron-up"></i></div>
                <div class="scroll-button scroll-button--down" @click="scrollDown()"><i class="fas fa-chevron-down"></i></div>
            </div> -->
        </div>

        <!-- <footer v-if="isAuthed" class="footer">
            &copy; Heba Pilates 2021
        </footer> -->
    </div>
</template>

<script>
    import axios from 'axios';
    import FormInput from '../components/FormInput.vue';
    import TheFooter from './layout/TheFooter.vue'
    import TheHeader from './layout/TheHeader.vue';

    export default {
        components: {
            FormInput,
            TheFooter,
            TheHeader
        },
        data () {
            return {
                isAuthed: (localStorage.getItem('tablet-token') || sessionStorage.getItem('tablet-token')) ? true : false,

                authUser: {
                    subscription: {},
                    details: {},
                    pending_sessions: [],
                    latest_notifications: []
                },

                memberAttended: null,
                trainerAttended: null,
                sessionRating: 0,
                hoverRating: 0,
                setRating: null,
                feedbackComments: '',
                feedbackSubmitted: false,

                showSwitchAccountModal: false,
                groups: [],
                burgerOpen: false,

                cookieConsent: localStorage.getItem('fc-cookie-consent') ? true : false,
                isAtTop: true

            };
        },

        mounted() {
            this.loadAuth();
        },

        watch: {
            $route (to, from){
                this.scrollToTop();
                $('html, body').removeClass('modal-active');
                this.loadAuth();
                this.burgerOpen = false;
            }
        },

        methods: {

            scrollToTop() {
                document.getElementById('app').scrollIntoView();
            },

            /**
             * Load the current authed admin.
             */
            loadAuth() {
                axios.get('/api/user').then(response => {
                    this.authUser = response.data;
                })
                .catch(error => {
                    console.log(error.response.data)
                    console.log('ERROR');

                    /*
                     * Invalid auth token.
                     */
                    if (error.response.data.message && error.response.data.message === 'Unauthenticated.') {
                        // if (this.$route.path !== '/login' && this.$route.path !== '/signup' && this.$route.path !== '/terms' && this.$route.path !== '/privacy') {
                        //     localStorage.removeItem('tablet-token');
                        //     sessionStorage.removeItem('tablet-token');
                        //     location.href = '/tablet';
                        // }
                    }
                });
            },

            scrollUp() {
                // window.scrollBy(0,-300)

                $("html").animate({
                    scrollTop: "-=" + "300px"
                }, function() {
                    // if (document.getElementById('html').scrollTop <= 0) {
                    //     this.isAtTop = true;
                    // } else {
                    //     this.isAtTop = false;
                    // }
                    //
                    // console.log('from top: ' + document.getElementById('html').scrollTop);
                    // console.log('is at top: ' + this.isAtTop);
                });
            },

            scrollDown() {

                // console.log('go down')
                // window.scrollBy(0,300)

                $("html").animate({
                    scrollTop: "+=" + "300px"
                }, function() {
                    // if (document.getElementById('html').scrollTop <= 0) {
                    //     this.isAtTop = true;
                    // } else {
                    //     this.isAtTop = false;
                    // }
                    //
                    // console.log('from top: ' + document.getElementById('html').scrollTop);
                    // console.log('is at top: ' + this.isAtTop);
                });
            }
        }
    };

</script>
