<template>
    <div>

        <div class="page-content">
            <!-- <VideoList
                title="Recommended"
                :videos="recommendedVideos"
                :loading="loading"
                :favourites="favouriteIds"
            /> -->

            <!-- <VideoList
                title="Browse Intermediate"
                :videos="intermediateVideos"
                :loading="loading"
                showAllLink="/" /> -->

            <VideoList
                title="Watch It Again"
                :videos="watchAgain"
                :loading="loading"
                :favourites="favouriteIds"
                v-if="watchAgain.length > 0"
            />

            <VideoList
                v-for="(category, index) in categories.filter(c => c.videos.length > 0)"
                :key="index"
                :title="category.name"
                :videos="category.videos"
                :loading="loading"
                :favourites="favouriteIds"
            />

            <VideoList
                title="Recommended For You"
                :videos="recommended"
                :loading="loading"
                :favourites="favouriteIds"
                v-if="recommended.length > 0"
            />

            <VideoList
                title="My Favourites"
                :videos="favourites"
                :loading="loading"
                :favourites="favouriteIds"
                v-if="favourites.length > 0"
            />
        </div>

    </div>
</template>

<script>
import axios from 'axios';
import VideoList from '../layout/VideoList.vue'
import FullscreenVideo from '../../components/FullscreenVideo.vue'

export default {
    components: { VideoList, FullscreenVideo },
    props: {
        authUser: Object
    },
    data () {
        return {
            categories: [],

            classes: [],
            recommended: [],
            continueWatching: [],
            watchAgain: [],

            favourites: [],
            favouriteIds: [],

            loading: true
        };
    },
    methods: {
        getClasses(){
            axios.get('/api/ondemand/categories').then(response => {
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
        }
    },
    beforeMount(){
        this.loadFavouriteIds();
    }
};
</script>
