<template>
    <div class="help page-content">
        <div v-if="!$route.query.q" class="wrapper">
            <h1>Search</h1>

            <search-input
                class="help__search"
                placeholder="Search On Demand, Live Classes, Categories, Instructors"
                @search="goToQuery" />
        </div>

        <VideoList
            v-else
            :title="'On Demand Results For: ' + this.$route.query.q"
            :videos="ondemandResults"
            :loading="loading"
            :favourites="favouriteIds"
            :verticalScroll="true"
        />
    </div>
</template>

<script>
import axios from 'axios';
import VideoList from './layout/VideoList.vue'
import SearchInput from '../components/SearchInput.vue';

export default {
    components: { VideoList, SearchInput },

    props: {
        authUser: Object
    },

    data () {
        return {
            ondemandResults: [],
            favouriteIds: [],
            loading: true
        };
    },

    watch: {
        '$route.query.q' () {
            if (this.$route.query.q)
                this.loadFavouriteIds();
        }
    },

    methods: {
        goToQuery (query) {
            if (query)
                this.$router.push('/search?q=' + query);
        },

        loadResults(){
            axios.get('/api/search?q=' + this.$route.query.q).then(response => {
                this.ondemandResults = response.data.ondemand;
                    this.loading = false;
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        loadFavouriteIds(){
            axios.get('/api/ondemand/favourites-by-id').then(response => {
                this.favouriteIds = response.data;
                this.loadResults();
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },
    },
    beforeMount(){
        if (this.$route.query.q)
            this.loadFavouriteIds();
    }
};
</script>
