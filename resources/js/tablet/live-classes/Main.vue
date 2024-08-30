<template>
    <div class="live-classes">

        <div class="page-content">
            <VideoList
                title="My Bookings"
                :videos="myBookings"
                :loading="loading"
                :isLive="true"
                :favourites="favouriteIds"
                v-if="myBookings.length > 0"
                />
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VideoList from '../layout/VideoList.vue'
import FullscreenVideo from '../../components/FullscreenVideo.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'
import FormInput from './../../components/FormInput.vue'

export default {
    components: { VideoList, FullscreenVideo, LoadingSpinner, FormInput },

    props: {
        authUser: Object
    },

    mounted() {
    },

    watch: {
        filterCategory: function (value) {
            this.$router.push('/live-classes/search?filterCategory=' + value);
        },
        filterDate: function (value) {
            var newDate = new Date(value);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            var dateFormatted = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
            this.$router.push('/live-classes/search?filterDate=' + dateFormatted);
        },
        filterDuration: function (value) {
            this.$router.push('/live-classes/search?filterDuration=' + value);
        },
    },

    data () {
        return {
            loading: true,
            classes: [],

            favourites: [],
            favouriteIds: [],

            thisWeeksClasses: [],
            nextWeeksClasses: [],
            myBookings: [],

            recommendedClasses: [],

            categories: [],

            filterDate: '',
            filterCategory: '',
            filterDuration: ''
        };
    },
    methods: {
        loadMyBookings() {
            axios.get('/api/live/bookings').then(response => {
                this.myBookings = response.data;
                this.loading = false;
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        /*
         * Load classes by period (this-week, next-week)
         */
        loadClassesByPeriod(period) {
            axios.get('/api/live/upcoming/' + period).then(response => {
                if (period === 'this-week') {
                    this.thisWeeksClasses = response.data;
                }

                else if (period === 'next-week') {
                    this.nextWeeksClasses = response.data;
                }

                this.loading = false;
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        loadCategories() {
            axios.get('/api/live/categories').then(response => {
                this.categories = response.data;
                this.loading = false;
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        openCategory(category) {
            this.$router.push('/live-classes/category/' + category.slug);
        },

        loadFavouriteIds(){
            axios.get('/api/live/favourites-by-id').then(response => {
                this.favouriteIds = response.data;
                this.loadMyBookings();
                // this.loadCategories();
                // this.loadRecommended();
                // this.loadClassesByPeriod('this-week');
                // this.loadClassesByPeriod('next-week');
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        loadRecommended() {
            axios.get('/api/live/recommended').then(response => {
                this.recommendedClasses = response.data;
            }).catch(error => {
                console.log('ERROR', error);
            });
        }
    },
    beforeMount() {
        this.loadFavouriteIds();
    }
};
</script>
