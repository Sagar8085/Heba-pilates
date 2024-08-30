<template>
    <div class="live-classes">

        <div class="page-content">
            <div class="wrapper">
                <LiveClassFilters
                    :date.sync="filterDate"
                    :category.sync="filterCategory"
                    :categories="categories"
                    :duration.sync="filterDuration" />
            </div>

            <VideoList
                title="My Bookings"
                :videos="myBookings"
                :loading="loading"
                :isLive="true"
                :favourites="favouriteIds"
                v-if="myBookings.length > 0"
                />

            <VideoList
                title="This Weeks Classes"
                :videos="thisWeeksClasses"
                :loading="loading"
                :favourites="favouriteIds"
                v-if="thisWeeksClasses.length > 0"
                :isLive="true" />

            <VideoList
                title="Upcoming Next Week"
                :videos="nextWeeksClasses"
                :loading="loading"
                :favourites="favouriteIds"
                v-if="nextWeeksClasses.length > 0"
                :isLive="true" />

            <VideoList
                title="Recommended For You"
                :videos="recommendedClasses"
                :loading="loading"
                :favourites="favouriteIds"
                v-if="recommendedClasses.length > 0"
                :isLive="true" />


            <section class="video-list">
                <header class="video-list__header wrapper">
                    <h3 class="video-list__header__title">Categories</h3>
                </header>
                <div class="video-list__container">
                    <div class="video-list__loading wrapper">
                        <loading-spinner
                            :loading="loading"
                            loadingText="categories"
                            :noData="categories.length == 0" />
                    </div>

                    <ul v-if="!loading && categories.length > 0" class="video-list__videos video-list__videos--categories">
                        <li
                            @click="openCategory(category)"
                            v-for="category in categories"
                            :key="category.id"
                            class="video-list__video">

                            <div class="video-list__video__image">
                                <img :src="category.image" />
                            </div>


                            <h4 class="video-list__video__title">
                                {{ category.name }}
                            </h4>

                            <p class="video-list__video__footer">
                                {{ category.description }}
                            </p>

                            <ul class="video-list__video__footer">
                                <button
                                    style="padding-left: 0; border-left: none; margin-left: -2px"
                                    :class="{ 'button button--icon button--plain button--transparent': true }"
                                    @click="openCategory(category)">
                                    <i class="material-icons">info</i>&nbsp;&nbsp;View Upcoming Classes
                                </button>
                            </ul>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VideoList from '../layout/VideoList.vue'
import FullscreenVideo from '../../components/FullscreenVideo.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'
import LiveClassFilters from '../../components/LiveClassFilters.vue'

export default {
    components: { VideoList, FullscreenVideo, LoadingSpinner, LiveClassFilters },

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
            this.$router.push('/live-classes/search?filterCategory=' + category.id);
        },

        loadFavouriteIds(){
            axios.get('/api/live/favourites-by-id').then(response => {
                this.favouriteIds = response.data;
                this.loadMyBookings();
                this.loadCategories();
                this.loadRecommended();
                this.loadClassesByPeriod('this-week');
                this.loadClassesByPeriod('next-week');
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
