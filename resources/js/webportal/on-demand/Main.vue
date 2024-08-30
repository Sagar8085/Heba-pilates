<template>
    <div>
        <div class="page-content">
            <loading-spinner
                class="wrapper"
                :loading="loading"
                loadingText="on demand videos"
            />

            <div class="wrapper">
                <section class="filter-bar">
                    <form-input v-model="filterDuration" type="select" placeholder="Duration" @change="filterResults">
                        <option value="">All Durations</option>
                        <option value="0-15">0 - 15 minutes</option>
                        <option value="16-30">16 - 30 minutes</option>
                        <option value="31-45">31 - 45 minutes</option>
                        <option value="45-60">45 mins - 1 Hour</option>
                        <option value="61">1 hour+</option>
                    </form-input>

                    <form-input v-model="filterInstructor" type="select" placeholder="Instructor">
                        <option value="">All Instructors</option>
                        <option :value="instructor.id" v-for="(instructor, index) in instructors">{{ instructor.name }}</option>
                    </form-input>

                    <form-input v-model="filterTag" type="select" placeholder="Other Options">
                        <option value="">All Options</option>
                        <option :value="tag.id" v-for="(tag, index) in tags">{{ tag.name }}</option>
                    </form-input>
                </section>
            </div>

            <VideoList
                title="Continue Watching"
                :videos="continueWatching"
                :loading="loading"
                :favourites="favouriteIds"
                v-if="continueWatching.length > 0"
            />

            <section class="next-journey wrapper" v-if="suggestedClass.id != null">
                <header class="video-list__header">
                    <h3 class="video-list__header__title">Next Class on your Heba Journey</h3>
                </header>

                <div class="next-journey__box">
                    <div class="next-journey__content">
                        <img :src="suggestedClass.image">
                        <div>
                            <h3>{{ suggestedClass.name }}</h3>
                            <p>Duration: {{ suggestedClass.duration }} Mins</p>
                            <router-link class="button" :to="'/on-demand/video/' + suggestedClass.id">Watch Now</router-link>
                        </div>
                    </div>
                </div>
            </section>

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

            <VideoList
                title="Watch It Again"
                :videos="watchAgain"
                :loading="loading"
                :favourites="favouriteIds"
                v-if="watchAgain.length > 0"
            />
        </div>

    </div>
</template>

<script>
import axios from 'axios';
import VideoList from '../layout/VideoList.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'
import FullscreenVideo from '../../components/FullscreenVideo.vue'
import FormInput from './../../components/FormInput.vue'

export default {
    components: { VideoList, FullscreenVideo, LoadingSpinner, FormInput },
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

            loading: true,

            suggestedClass: {},

            filterDuration: '',
            filterInstructor: '',
            filterTag: '',

            instructors: [],
            tags: []
        };
    },

    watch: {
        filterDuration() {
            this.loadFavouriteIds();
        },

        filterInstructor() {
            this.loadFavouriteIds();
        },

        filterTag() {
            this.loadFavouriteIds();
        }
    },

    methods: {
        getClasses(){
            axios.get('/api/ondemand/categories?duration=' + this.filterDuration + '&instructor=' + this.filterInstructor + '&tag=' + this.filterTag).then(response => {
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
            axios.get('/api/ondemand/favourites?duration=' + this.filterDuration + '&instructor=' + this.filterInstructor + '&tag=' + this.filterTag).then(response => {
                this.favourites = response.data;
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        loadRecommended() {
            axios.get('/api/ondemand/recommended?duration=' + this.filterDuration + '&instructor=' + this.filterInstructor + '&tag=' + this.filterTag).then(response => {
                this.recommended = response.data;
            }).catch(error => {
                console.log('ERROR', error);
            });
        },

        loadContinueWatching() {
            axios.get('/api/ondemand/continue-watching?duration=' + this.filterDuration + '&instructor=' + this.filterInstructor + '&tag=' + this.filterTag).then(response => {
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
        },

        filterResults() {
            console.log('chagnign')
        },

        loadTags() {
            axios.get('/api/ondemand/tags').then(response => {
                this.tags = response.data;
            });
        },

        loadInstructors() {
            axios.get('/api/ondemand/instructors').then(response => {
                this.instructors = response.data;
            });
        }
    },
    beforeMount(){
        this.loadInstructors();
        this.loadTags();
        this.loadFavouriteIds();
    }
};
</script>
