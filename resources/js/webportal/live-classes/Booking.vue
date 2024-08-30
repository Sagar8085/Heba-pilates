<template>
    <div class="live-class page-content wrapper">
        <breadcrumbs :links="breadcrumbLinks" />

        <div class="row">
            <div class="columns eight-lg">
                <h2>Where will you take this class?</h2>
                <p style="margin-bottom: 1.5rem;" v-if="liveclass.reformer">This Live Class requires use of a Heba Pilates Reformer and can only be attended at one of our studios.</p>
                <div class="row">
                    <div class="columns six-md">
                        <form-input
                            v-model="studioOrHome"
                            label=""
                            type="select"
                            staticLabel
                            placeholder="Select"
                            style="max-width: 400px">
                            <option value="studio">In Studio</option>
                            <option value="home" v-if="!liveclass.reformer">At Home</option>
                        </form-input>
                    </div>
                </div>

                <h2 v-if="studioOrHome === 'studio'">Select Studio</h2>
                <div class="row" v-if="studioOrHome === 'studio'">
                    <div class="columns six-md">
                        <form-input
                            v-model="studio"
                            label=""
                            type="select"
                            staticLabel
                            placeholder="Select one"
                            style="max-width: 400px"
                            >
                            <option :value="gym.id" v-for="gym in gyms" :key="gym.id">{{ gym.name }}</option>
                        </form-input>
                    </div>
                </div>

                <div v-if="(studioOrHome === 'home' || studioOrHome === 'studio' && studio != '')">
                    <h3 class="video-list__header__title">Book Class</h3>

                    <div class="membership account__section" v-if="this.authUser.subscription !== null">
                        <ul class="booking-list" v-if="(this.studioOrHome === 'home' && this.authUser.subscription.online_credits > 0 || this.studioOrHome === 'studio' && this.authUser.subscription.studio_credits > 0)">
                            <li class="booking-list__booking">
                                <div class="booking-list__booking__section">
                                    <h4 class="page-subtitle">My Membership</h4>
                                    <p class="booking-list__booking__section__value booking-list__booking__section__value--large">
                                        {{ this.authUser.subscription.name }}
                                    </p>
                                </div>
                                <div class="booking-list__booking__section" v-if="studioOrHome === 'home'">
                                    <h4 class="page-subtitle">Online Credits Remaining</h4>
                                    <p class="booking-list__booking__section__value">{{ this.authUser.subscription.online_credits_human }}</p>
                                </div>
                                <div class="booking-list__booking__section" v-if="studioOrHome === 'studio'">
                                    <h4 class="page-subtitle">In-Studio Credits Remaining</h4>
                                    <p class="booking-list__booking__section__value">{{ this.authUser.subscription.studio_credits_human }}</p>
                                </div>
                                <div class="booking-list__booking__section">
                                    <button class="button" @click="bookClass('membership')">Book Now (1 Credit)</button>
                                </div>
                            </li>
                        </ul>

                        <div v-else>
                            <p style="margin: 2rem 0;">You don't have any credits available in your subscription, you can use a credit from your Credit Packs, if you don't have any available, you can purchase a credit pack from your profile settings.</p>

                            <section class="account__section membership__credit-packs">
                                <h3 class="page-subtitle">My Credit Packs</h3>

                                <ul class="booking-list">
                                    <li class="booking-list__booking" v-for="(creditPack, index) in this.creditPackPurchases" :key="index">
                                        <div class="booking-list__booking__section">
                                            <h4 class="page-subtitle">Type</h4>
                                            <p class="booking-list__booking__section__value booking-list__booking__section__value--large">{{ creditPack.pack.name }}</p>
                                        </div>
                                        <div class="booking-list__booking__section">
                                            <h4 class="page-subtitle">Online Credits</h4>
                                            <p class="booking-list__booking__section__value">{{ creditPack.online_credits }} Remaining</p>
                                        </div>
                                        <div class="booking-list__booking__section">
                                            <h4 class="page-subtitle">In-Studio Credits</h4>
                                            <p class="booking-list__booking__section__value">{{ creditPack.studio_credits }} Remaining</p>
                                        </div>
                                        <div class="booking-list__booking__section">
                                            <button class="button" @click="bookClass('credit', creditPack.id)" v-if="(studioOrHome === 'home' && creditPack.online_credits > 0 || studioOrHome === 'studio' && creditPack.studio_credits > 0)">Book (1 Credit)</button>
                                        </div>
                                    </li>
                                    <li class="booking-list__booking booking-list__booking--2-cols">
                                        <div class="booking-list__booking__section">
                                            <h4 class="page-subtitle">Additional Credit Packs</h4>
                                            <p class="booking-list__booking__section__value booking-list__booking__section__value--large">
                                                Purchase Credits
                                            </p>
                                        </div>
                                        <div class="booking-list__booking__section">
                                            <router-link class="button" to="/membership">Purchase Credits</router-link>
                                        </div>
                                    </li>
                                </ul>
                            </section>

                        </div>
                        <p style="margin-top: 2rem;">You can view our <router-link to="/terms">Cancellation</router-link> and <router-link to="/terms">Refund Policy</router-link> Terms and Conditions at any time.</p>
                    </div>

                    <div class="membership account__section">
                        <section class="account__section membership__credit-packs">
                            <h3 class="page-subtitle">My Credit Packs</h3>

                            <ul class="booking-list">
                                <li class="booking-list__booking" v-for="(creditPack, index) in this.creditPackPurchases" :key="index">
                                    <div class="booking-list__booking__section">
                                        <h4 class="page-subtitle">Type</h4>
                                        <p class="booking-list__booking__section__value booking-list__booking__section__value--large">{{ creditPack.pack.name }}</p>
                                    </div>
                                    <div class="booking-list__booking__section">
                                        <h4 class="page-subtitle">Online Credits</h4>
                                        <p class="booking-list__booking__section__value">{{ creditPack.online_credits }} Remaining</p>
                                    </div>
                                    <div class="booking-list__booking__section">
                                        <h4 class="page-subtitle">In-Studio Credits</h4>
                                        <p class="booking-list__booking__section__value">{{ creditPack.studio_credits }} Remaining</p>
                                    </div>
                                    <div class="booking-list__booking__section">
                                        <button class="button" @click="bookClass('credit', creditPack.id)" v-if="(studioOrHome === 'home' && creditPack.online_credits > 0 || studioOrHome === 'studio' && creditPack.studio_credits > 0)">Book (1 Credit)</button>
                                    </div>
                                </li>
                                <li class="booking-list__booking booking-list__booking--2-cols">
                                    <div class="booking-list__booking__section">
                                        <h4 class="page-subtitle">Additional Credit Packs</h4>
                                        <p class="booking-list__booking__section__value booking-list__booking__section__value--large">
                                            Purchase Credits
                                        </p>
                                    </div>
                                    <div class="booking-list__booking__section">
                                        <router-link class="button" to="/membership">Purchase Credits</router-link>
                                    </div>
                                </li>
                            </ul>
                        </section>

                    </div>
                </div>
            </div>
            <div class="columns four-lg">
                <h2 class="live-class__title live-class__title--no-top">Booking Summary</h2>

                <img class="img-responsive" :src="liveclass.category.image" />

                <h2 class="live-class__title">{{ liveclass.category.name }}</h2>
                <h3 class="page-subtitle">{{ liveclass.datetime_human }}</h3>

                <div v-if="liveclass.equipment && liveclass.equipment.length > 0" class="live-class__row">
                    <p class="live-class__details">
                        <strong>Equipment</strong>
                        {{ liveclass.equipment.map(x => x.name).join(', ') }}
                    </p>
                </div>

                <div class="live-class__row">
                    <span class="live-class__time">{{ liveclass.time_human }}</span>
                    <p class="live-class__details">
                        <strong>Duration</strong>
                        {{ liveclass.duration }} minutes
                    </p>
                </div>

                <div class="live-class__row">
                    <p class="live-class__details">
                        <strong>No. of Attendees</strong>
                        {{ bookingCount }}
                    </p>
                </div>
            </div>
        </div>

        <Modal v-model="processingBooking" title="Booking In Progress" hideCancel hideClose>
            We are booking your class, one moment please
            <br><br>
            <i class="fas fa-circle-notch fa-spin fa-2x"></i>
        </Modal>

        <Modal v-model="bookingFailure" title="Unable to process booking" hideCancel hideClose>
            {{ bookingFailureMessage }}
            <template slot="buttons">
                <button class="button button--full" @click="bookingFailure = false">Okay</button>
            </template>
        </Modal>

        <Modal v-model="bookingSuccess" title="Booking Complete" hideCancel hideClose>
            Great! Your booking has been accepted, you can view your upcoming bookings with your 'My Bookings' page.
            <template slot="buttons">
                <router-link to="/bookings" class="button button--full" style="color: white;">View My Bookings</router-link>
            </template>
        </Modal>
    </div>
</template>

<script>
import Breadcrumbs from '../../components/Breadcrumbs.vue'
import Modal from '../../components/Modal.vue'
import StarRating from '../layout/StarRating.vue'
import axios from 'axios'
import FormInput from '../../components/FormInput.vue';

export default {
    components: { StarRating, Breadcrumbs, FormInput, Modal },

    props: {
        authUser: Object
    },

    data () {
        return {
            liveclass: {
                category: {}
            },

            bookingCount: 'Loading...',
            averageRating: 0,

            booking: {},

            studioOrHome: '',
            studio: '',

            gyms: [],

            processingBooking: false,
            bookingFailure: false,
            bookingFailureMessage: '',
            bookingSuccess: false,

            requiresCreditPurchase: false,

            creditPackPurchases: []
        }
    },

    mounted() {
        this.loadLiveClass();
        this.loadBookingCount();
        this.loadRating();
        this.loadGyms();
        this.checkBooking();
        this.loadCreditPackPurchases()
    },

    methods: {
        checkBooking() {
            axios.get('/api/live/class/' + this.$route.params.id + '/booking').then(response => {
                this.booking = response.data;
            }).catch(error => {
                console.log('ERROR', error);
            });
        },

        loadLiveClass() {
            axios.get('/api/live/class/' + this.$route.params.id).then(response => {
                this.liveclass = response.data;
                this.loading = false;

                if (this.liveclass.reformer) {
                    console.log('setting')
                    this.studioOrHome = 'studio';
                }
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        loadBookingCount() {
            axios.get('/api/live/class/' + this.$route.params.id + '/bookings').then(response => {
                this.bookingCount = response.data;
            });
        },

        loadRating() {
            axios.get('/api/live/class/' + this.$route.params.id + '/average-rating').then(response => {
                this.averageRating = response.data;
            });
        },

        loadGyms() {
            axios.get('/api/gyms').then(response => {
                this.gyms = response.data;
            });
        },

        bookClass(type, creditPackPurchaseId = null) {
            this.processingBooking = true;

            console.log('booking class');
            axios.post('/api/live/class/' + this.$route.params.id + '/book', {
                studioOrHome: this.studioOrHome,
                gym_id: this.studio,
                paymentType: type,
                creditPackPurchaseId: creditPackPurchaseId
            }).then(response => {
                console.log('got response')
                console.log(response)
                if (response.data.status === 'failure') {
                    this.bookingFailure = true;
                    this.bookingFailureMessage = response.data.message;
                    return;
                } else {
                    this.bookingSuccess = true;
                }
            }).catch(error => {
                console.log('ERROR', error);
                this.processingBooking = false;
            }).finally(() => {
                this.processingBooking = false;
            });
        },

        /*
         * Load all credit packs this user has purchased.
         * @param {none}
         */
        loadCreditPackPurchases() {
            axios.get('/api/account/my-credit-packs').then(response => {
                this.creditPackPurchases = response.data;
            });
        },
    },

    computed: {
        breadcrumbLinks () {
            return [
                { link: '/live-classes', title: 'Live Classes' },
                { link: '/live-classes/category/' + this.liveclass.category.slug, title: this.liveclass.category.name },
                { link: '/live-classes/class/' + this.$route.params.id, title: this.liveclass.datetime_human },
                { link: '/live-classes/class/' + this.$route.params.id + '/book', title: 'Book Class' },
            ]
        }
    }
}
</script>
