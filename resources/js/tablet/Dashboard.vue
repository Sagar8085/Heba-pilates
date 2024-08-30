<template>
    <div style="padding-bottom: 1.5rem;">
        <div class="home" style="padding-bottom: 1.5rem;">
            <h1 class="home__title" style="padding-bottom: 1.5rem;">Hello {{this.authUser.first_name}}!</h1>
            <section class="filter-bar">
                <multiselect
                    v-model="filterBodyFocus"
                    :options="bodyFocuses"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    placeholder="Body Focus"
                    label="name"
                    track-by="slug"
                    :searchable="false"
                >
                    <template slot="caret">
                        <i class="multiselect__caret fas fa-chevron-down"></i>
                    </template>
                </multiselect>

                <multiselect
                    v-model="filterTheme"
                    :options="themes"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    placeholder="Theme"
                    label="name"
                    track-by="slug"
                    :searchable="false"
                >
                    <template slot="caret">
                        <i class="multiselect__caret fas fa-chevron-down"></i>
                    </template>
                </multiselect>

                <multiselect
                    v-model="filterConsiderations"
                    :options="considerations"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    placeholder="Considerations"
                    label="name"
                    track-by="slug"
                    :searchable="false"
                >
                    <template slot="caret">
                        <i class="multiselect__caret fas fa-chevron-down"></i>
                    </template>
                </multiselect>
            </section>
        </div>

        <section class="next-journey" v-if="suggestedClass.id != null">
            <header class="video-list__header">
                <h3 class="video-list__header__title">Next Class on your Heba Journey</h3>
            </header>

            <div class="next-journey__box">
                <div class="next-journey__content">
                    <img :src="suggestedClass.image">
                    <div>
                        <h3>{{ suggestedClass.name }}</h3>
                        <p>Duration: {{ suggestedClass.duration }} Mins</p>
                        <router-link class="button" :to="'/tablet/on-demand/video/' + suggestedClass.id">Watch Now</router-link>
                    </div>
                </div>
            </div>
        </section>

        <VideoList
            title="Recommended For You"
            :videos="recommended"
            :scroller="'recommended'"
            :loading="loading"
            :favourites="favouriteIds"
            v-if="recommended.length > 0"
            scroll-indicators
        />

        <VideoList
            title="My Favourites"
            :videos="favourites"
            :scroller="'favs'"
            :loading="loading"
            :favourites="favouriteIds"
            v-if="favourites.length > 0"
            scroll-indicators
        />

        <VideoList
            title="Watch It Again"
            :videos="watchAgain"
            :scroller="'again'"
            :loading="loading"
            :favourites="favouriteIds"
            v-if="watchAgain.length > 0"
            scroll-indicators
        />

        <VideoList
            v-for="(category, index) in categories.filter(c => c.videos.length > 0)"
            :key="index"
            :scroller="category.id"
            :title="category.name"
            :videos="category.videos"
            :loading="loading"
            :favourites="favouriteIds"
            scroll-indicators
        />

        <!-- <VideoList
            title="Live Class Bookings"
            :videos="myBookings"
            :loading="loading"
            :isLive="true"
            :favourites="[]"
            style="margin-top: 3rem;"
            v-if="myBookings.length > 0"
            /> -->
    </div>
</template>

<script>
    import axios from 'axios';
    import VideoList from './layout/VideoList.vue'
    import FormInput from '../components/FormInput.vue'
    import Multiselect from 'vue-multiselect';

    export default {
        components: { VideoList, FormInput, Multiselect },

        props: {
            authUser: Object
        },

        data () {
            return {
                user: {
                    subscription: {}
                },
                sessions: [],
                trainers: [],
                claiming: false,
                trialClaimed: false,
                trialPackage: {},
                selectedTrainer: {},
                pending_subscription: {},
                suggestedClasses: [],
                myBookings: [],
                categories: [],

                bodyFocuses: [
                    {slug: 'total-body', name: 'Total body'},
                    {slug: 'upper-body', name: 'Upper body'},
                    {slug: 'lower-body', name: 'Lower body'},
                    {slug: 'abdominals', name: 'Abdominals'},
                    {slug: 'lower-back', name: 'Lower back'},
                    {slug: 'glutes', name: 'Glutes'},
                ],
                themes: [
                    {slug: 'go-steady', name: 'Go Steady'},
                    {slug: 'get-mobile', name: 'Get Mobile'},
                    {slug: 'feel-strong', name: 'Feel Strong'},
                    {slug: 'be-adventurous', name: 'Be Adventurous'},
                ],
                considerations: [
                    {slug: 'jump-board', name: 'Jump board'},
                    {slug: 'split-bench', name: 'Split bench'},
                    {slug: 'dome', name: 'Dome'},
                    {slug: 'yellow-ropes', name: 'Yellow ropes'},
                    {slug: 'pregnancy', name: 'Pregnancy'},
                ],

                filterBodyFocus: [],
                filterTheme: [],
                filterConsiderations: [],

                classes: [],
                recommended: [],
                continueWatching: [],
                watchAgain: [],

                favourites: [],
                favouriteIds: [],

                loading: true,

                suggestedClass: {},
            };
        },

        mounted() {
            this.loadSuggestedClasses();
            this.loadMyBookings();
            this.loadFavouriteIds();
        },

        computed: {
            filterTags() {
                const filterBodyFocus = this.filterBodyFocus.map(f => f.slug);
                const filterTheme = this.filterTheme.map(f => f.slug);
                const filterConsiderations = this.filterConsiderations.map(f => f.slug);

                return [...filterBodyFocus, ...filterTheme, ...filterConsiderations];
            },
        },

        watch: {
            filterBodyFocus() {
                this.loadFavouriteIds();
            },

            filterTheme() {
                this.loadFavouriteIds()
            },

            filterConsiderations() {
                this.loadFavouriteIds()
            },

        },

        methods: {
            loadSuggestedClasses() {
                axios.get('/api/ondemand/suggested').then(response => {
                    this.suggestedClasses = response.data;
                })
            },
            loadMyBookings() {
                axios.get('/api/live/bookings').then(response => {
                    this.myBookings = response.data;
                    this.loading = false;
                }).catch(error => {
                    console.log('ERROR', error);
                    this.loading = false;
                });
            },
            getClasses(){
                axios.get('/api/ondemand/categories', {
                    params: {
                        tags: this.filterTags,
                    },
                }).then(response => {
                    this.loading = false;
                    this.categories = response.data;
                }).catch(error => {
                    console.log('ERROR', error);
                    this.loading = false;
                });
            },

            loadFavouriteIds(){
                axios.get('/api/ondemand/favourites-by-id').then(response => {
                    this.favouriteIds = response.data;
                    this.getClasses();
                    this.loadFavourites();
                    this.loadRecommended();
                    this.loadContinueWatching();
                    this.loadSuggestedClass();
                }).catch(error => {
                    console.log('ERROR', error);
                    this.loading = false;
                });
            },

            loadFavourites(){
                axios.get('/api/ondemand/favourites').then(response => {
                    this.favourites = response.data;
                }).catch(error => {
                    console.log('ERROR', error);
                    this.loading = false;
                });
            },

            loadRecommended() {
                axios.get('/api/ondemand/recommended').then(response => {
                    this.recommended = response.data;
                }).catch(error => {
                    console.log('ERROR', error);
                });
            },

            loadContinueWatching() {
                axios.get('/api/ondemand/continue-watching').then(response => {
                    this.continueWatching = response.data.continue_watching;
                    this.watchAgain = response.data.watch_again;
                }).catch(error => {
                    console.log('ERROR', error);
                });
            },

            loadSuggestedClass() {
                axios.get('/api/ondemand/suggested').then(response => {
                    this.suggestedClass = response.data;
                })
            }
        }
    };
</script>
