<template>
    <div v-if="loading || !liveclass.id" class="live-class page-content wrapper">
        <loading-spinner :loading="loading" :noData="!liveclass.id" noDataText="Class not found." />
    </div>

    <div v-else class="live-class page-content wrapper">
        <breadcrumbs :links="breadcrumbLinks" />

        <div class="row">
            <div class="columns six-lg">
                <img class="img-responsive" :src="liveclass.category.image" />
            </div>
            <div class="columns six-lg">

                <label class="page-subtitle">{{ averageRating > 0 ? 'Average Rating' : 'Be the first to rate this class!' }}</label>
                <star-rating :rating="averageRating" />

                <h2 class="live-class__title">{{ liveclass.category.name }}</h2>
                <h3 class="page-subtitle">{{ liveclass.datetime_human }}</h3>

                <div class="live-class__row">
                    <button class="button button--transparent button--with-icon button--plain">
                        <i class="material-icons">{{ liveclass.favourite ? 'favorite' : 'favorite_border' }}</i>
                        Favourite
                    </button>
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

                <div class="live-class__row" v-if="booking.id == null">
                    <router-link :to="'/live-classes/class/' + liveclass.id + '/book'" class="button button--full">Continue to Booking</router-link>
                </div>

                <div class="live-class__row" v-else>
                    <a :href="'/live/' + booking.liveclass_id + '/stream'" class="button button--full" v-if="!liveclass.reformer">Join Class Stream</a>
                    <p v-if="liveclass.reformer" style="font-weight: bold; font-style: italic;">To join this stream, you must access this class via your in-studio tablet</p>
                </div>
            </div>
        </div>
        <div class="row row--center">
            <div class="columns columns--half">

                <div class="live-class__row">
                    <p class="live-class__details">
                        <strong>Equipment</strong>
                        <p v-for="(equipment, index) in liveclass.equipment">{{ equipment.name }},&nbsp;</p>
                    </p>
                </div>

                <!-- <div class="live-class__row">
                    <p class="live-class__details">
                        <strong>Setup Position</strong>
                        {{ liveclass.setup_position }}
                    </p>
                </div> -->

                <!-- <div class="row">
                    <div class="columns six-sm">
                        <img class="img-responsive" src="/images/MeetTheExpertsCard1.png" alt="Live Class Image" />
                    </div>
                    <div class="columns six-sm">
                        <img class="img-responsive" src="/images/MeetTheExpertsCard1.png" alt="Live Class Image" />
                    </div>
                </div> -->

                <!-- <div class="live-class__row live-class__trainer">
                    <img src="/images/MeetTheExpertsCard1.png" alt="Trainer Profile Picture" />
                    <span>Teacher's Name</span>
                </div> -->

                <div class="live-class__row">
                    <p class="live-class__details">
                        <strong>Class Description</strong>
                        {{ liveclass.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Breadcrumbs from '../../components/Breadcrumbs.vue'
import StarRating from '../layout/StarRating.vue'
import axios from 'axios'
import LoadingSpinner from '../layout/LoadingSpinner.vue'

export default {
    components: { StarRating, Breadcrumbs, LoadingSpinner },

    data () {
        return {
            liveclass: {
                category: {},

                name: 'Class Name',
                rating: 4,
                time_human: '14:30',
                category: 'Category',
                favourite: false,
                duration: 60,
                attendees: 33,
                equipment: 'Nuforma',
                setup_position: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam auctor maximus mauris in congue. Quisque fermentum porta nulla, sed interdum purus maximus ut. Nulla dictum est sit amet arcu accumsan iaculis. Duis mi sem, cursus ac metus a, fermentum dapibus quam. Vestibulum gravida enim vel neque dapibus tincidunt. Pellentesque luctus ipsum purus, eget consectetur arcu condimentum nec.',
                description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam auctor maximus mauris in congue. Quisque fermentum porta nulla, sed interdum purus maximus ut. Nulla dictum est sit amet arcu accumsan iaculis. Duis mi sem, cursus ac metus a, fermentum dapibus quam. Vestibulum gravida enim vel neque dapibus tincidunt. Pellentesque luctus ipsum purus, eget consectetur arcu condimentum nec.'
            },

            bookingCount: 'Loading...',
            averageRating: 0,
            loading: true,
            booking: {}
        }
    },

    mounted() {
        this.loadLiveClass();
        this.loadBookingCount();
        this.loadRating();
        this.checkBooking();
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
        }
    },

    computed: {
        breadcrumbLinks () {
            return [
                { link: '/live-classes', title: 'Live Classes' },
                { link: '/live-classes/category/' + this.liveclass.category.slug, title: this.liveclass.category.name },
                { link: '/live-classes/class/' + this.$route.params.id, title: this.liveclass.datetime_human },
            ]
        }
    }
}
</script>
