<template>
    <div :class="appClasses">
        <the-header
            v-if="!hideHeaderAndFooter"
            ref="header"
            :isBurgerOpen.sync="burgerOpen"
            :authUser="authUser"
            :isAuthed="isAuthed" />

        <div class="app-wrap">
            <router-view :authUser="this.authUser"></router-view>
        </div>

        <the-footer v-if="isAuthed && !hideHeaderAndFooter" @open-terms="showTermsPopup = true" />
        <footer v-else-if="!$route.path.includes('/onboard')" class="footer">
            &copy; Heba Pilates 2021
        </footer>

        <section v-if="authUser.pending_sessions.length > 0">
            <div class="modal modal--active" v-for="(session, index) in authUser.pending_sessions">
                <div class="modal__box modal__box--small modal__body">
                    <div v-if="memberAttended === null">
                        <h2 class="modal__title">Rate Your Session <span v-if="authUser.pending_sessions.length > 1">({{ (index) + ' of ' + authUser.pending_sessions.length}})</span></h2>

                        <p>Your session at {{ session.time_human }} is currently awaiting your feedback, please let us know if you attended this session and give your coach a rating.</p>

                        <div class="modal__divider"></div>

                        <div class="booking-card booking-card--no-box">
                            <section class="booking-card__header">
                                <div>
                                    <p class="booking-card__header__subtitle">
                                        <template>
                                            {{ session.time_human }}
                                        </template>
                                    </p>
                                    <dl class="booking-card__header__list">
                                        <div class="booking-card__header__list__item">
                                            <dt>Trainer</dt>
                                            <dd>{{ session.trainer.name }}</dd>
                                        </div>

                                        <div class="booking-card__header__list__item">
                                            <dt>Duration</dt>
                                            <dd>{{ session.length_human }} Minutes</dd>
                                        </div>
                                    </dl>

                                    <div>
                                        <p class="modal__body">Did you attend this session?</p>

                                        <button class="button button" @click="memberAttended = true">Yes</button>
                                        <button class="button button--outline-red" @click="memberAttended = false; submitFeedback(session.id)">No</button>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div v-if="memberAttended">
                        <h2 class="modal__title">Thanks {{ authUser.first_name }}!</h2>

                        <p v-if="setRating === null">We just need to you answer a couple more questions and we'll have you on your way!</p>
                        <p v-else>Nearly there now!</p>

                        <div class="modal__divider"></div>

                        <div v-if="trainerAttended === null">
                            <p>Did your Virtual Coach attend the session?</p>

                            <br>

                            <button class="button button--outline" @click="trainerAttended = true">Yes</button>
                            <button class="button button--outline-red" @click="trainerAttended = false; submitFeedback(session.id)">No</button>
                        </div>

                        <div v-if="trainerAttended && setRating === null">
                            <p>Awesome, how would you rate your session out of 5?</p>

                            <br>

                            <div class="star-rating">
                                <i :class="hoverRating < index ? 'far fa-star' : 'fas fa-star gold'" v-for="index in 5" @mouseover="hoverRating = index" @click="setRating = index"></i>
                            </div>
                        </div>

                        <div v-if="setRating !== null">
                            <p>Would you like to add any additional comments about your session or your coach?</p>

                            <br>

                            <div class="form-row">
                                <form-input v-model="feedbackComments" type="text" label="Additional Feedback (Optional)" style="padding-top: 10px;" />
                            </div>

                            <br>

                            <button class="button" @click="submitFeedback(session.id)">Submit Rating & Feedback</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal modal--active" v-if="feedbackSubmitted">
            <div class="modal__box modal__box--small modal__body">
                <div class="star-rating">
                    <i class="far fa-check-circle green"></i>
                </div>

                <h2 class="modal__title">Feedback Submitted</h2>

                <p>Thanks {{ authUser.first_name }}, your feedback of our sessions and trainers helps us make The Fitness Concierge even better! We take all feedback seriously and use it to bring you better a experience and even more features!</p>

                <div class="modal__divider"></div>


                <button class="button" @click="feedbackSubmitted = false; loadAuth();">Done</button>
            </div>
        </div>

        <section class="cookie-consent" v-if="!cookieConsent">
            <div class="cookie-consent__text">
                <p>Heba Pilates (app.hebapilates.com) uses cookies to allow basic use of this platform, these are not used for tracking or marketing purposes.</p>
            </div>

            <div class="cookie-consent__button">
                <button class="button button--white" @click="applyCookieConsent()">Okay, Thanks</button>
            </div>
        </section>

        <terms-popup v-model="showTermsPopup" />
    </div>
</template>

<script>
    import axios from 'axios';
    import FormInput from '../components/FormInput.vue';
    import TheFooter from './layout/TheFooter.vue'
    import TheHeader from './layout/TheHeader.vue';
    import TermsPopup from './legal/TermsPopup.vue';

    export default {
        components: {
            FormInput,
            TheFooter,
            TheHeader,
            TermsPopup
        },
        data () {
            return {
                isAuthed: (localStorage.getItem('fc-usertoken') || sessionStorage.getItem('fc-usertoken')) ? true : false,

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
                showTermsPopup: false,

                cookieConsent: localStorage.getItem('fc-cookie-consent') ? true : false,

            };
        },

        mounted() {
            this.loadAuth();

            console.log('cookie consent = ' + this.cookieConsent);
        },

        watch: {
            $route (to, from){
                this.scrollToTop();
                $('html, body').removeClass('modal-active');

                if (localStorage.getItem('fc-usertoken') != null || sessionStorage.getItem('fc-usertoken') != null) {
                    this.loadAuth();
                    if (this.$refs.header) this.$refs.header.closeDropdowns();
                }

                this.burgerOpen = false;
            }
        },

        computed: {
            isSubscriptionLandingPage () {
                return this.$route.name?.includes('SubscriptionLandingPage') ?? false;
            },

            hideHeaderAndFooter () {
                return this.$route.path == '/login'
                    || this.$route.path == '/signup'
                    || this.$route.path == '/forgot'
                    || this.$route.name == 'ResetPassword'
                    || this.$route.name == 'ConfirmAccount'
                    || this.$route.path.includes('/onboard')
                    || this.isSubscriptionLandingPage;
            },
            appClasses () {
                return {
                    'login': this.$route.path === '/login',
                    'sign-up': this.$route.name === 'Register',
                    'onboard': this.$route.path.includes('/onboard'),
                    'home': this.$route.path === '/',
                    'forgot': this.$route.path === '/forgot' || this.$route.name == 'ResetPassword' || this.$route.name == 'ConfirmAccount',
                    'subscription-lp': this.isSubscriptionLandingPage
                }
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
                        if (this.$route.path !== '/login' && this.$route.path !== '/signup' && this.$route.path !== '/terms' && this.$route.path !== '/privacy' && !this.$route.path.includes('/onboard') && !this.$route.path.includes('/reset') && !this.isSubscriptionLandingPage) {
                            localStorage.removeItem('fc-usertoken');
                            sessionStorage.removeItem('fc-usertoken');
                            location.href = '/login';
                        }
                    }
                });
            },

            submitFeedback(session_id) {
                if (!this.memberAttended) {
                    var status = 'noshow';
                } else if (!this.trainerAttended) {
                    var status = 'trainer-noshow';
                } else {
                    var status = 'completed';
                }

                axios.post('/api/virtual/session/' + session_id + '/feedback', {
                    rating: this.setRating,
                    feedback: this.feedbackComments,
                    status: status
                }).then(response => {
                    this.memberAttended = null;
                    this.trainerAttended = null;
                    this.hoverRating = 0;
                    this.setRating = null;
                    this.authUser.pending_sessions = [];
                    this.feedbackComments = '';
                    this.feedbackSubmitted = true;
                })
                .catch(error => {
                    // console.log('ERROR');
                });
            },

            applyCookieConsent() {
                localStorage.setItem('fc-cookie-consent', 'yes');
                this.cookieConsent = true;
            }
        }
    };

</script>
