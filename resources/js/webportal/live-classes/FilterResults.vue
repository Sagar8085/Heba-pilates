<template>
    <div class="live-classes">

        <div class="page-content">
            <div class="wrapper">
                <LiveClassFilters
                    :date.sync="filterDate"
                    :category.sync="filterCategory"
                    :categories="categories"
                    :duration.sync="filterDuration"
                    :allOption="true" />
            </div>
            
            <loading-spinner class="wrapper" :loading="loading" loadingText="live classes" :noData="results.length == 0">
                <template slot="no-data">
                    We have no live classes to show you using your search parameters. Try expanding your search.
                    <br/>
                    <router-link class="button" to="/live-classes">View Upcoming Live Classes</router-link>    
                </template>
            </loading-spinner>

            <VideoList
                v-if="results.length > 0"
                title="Live Classes"
                :videos="results"
                :isLive="true"
                :favourites="favouriteIds"
                :verticalScroll="true"
            />
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VideoList from '../layout/VideoList.vue'
import LiveClassFilters from './../../components/LiveClassFilters.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'

export default {
    components: { VideoList, LiveClassFilters, LoadingSpinner },

    props: {
        authUser: Object
    },

    mounted() {
        if (this.$route.query.filterCategory) {
            this.filterCategory = this.$route.query.filterCategory;
        }

        if (this.$route.query.filterDate) {
            this.filterDate = new Date(this.$route.query.filterDate);
            this.dateFormatted = this.$route.query.filterDate;
        }

        if (this.$route.query.filterDuration) {
            this.filterDuration = this.$route.query.filterDuration;
        }
    },

    watch: {
        filterCategory: function () {
            this.loadResults();
        },

        filterDuration: function () {
            this.loadResults();
        },

        filterDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.dateFormatted = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
            this.loadResults();
        }
    },

    data () {
        return {
            loading: true,

            favourites: [],
            favouriteIds: [],

            results: [],

            categories: [],

            filterDate: '',
            filterCategory: '',
            filterDuration: '',
            dateFormatted: ''
        };
    },
    methods: {
        loadCategories() {
            axios.get('/api/live/categories').then(response => {
                this.categories = response.data;
            }).catch(error => {
                console.log('ERROR', error);
            });
        },

        loadFavouriteIds(){
            axios.get('/api/live/favourites-by-id').then(response => {
                this.favouriteIds = response.data;
                this.loadCategories();
                this.loadResults();
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        loadResults() {
            if (!this.filterDate && !this.filterCategory && !this.filterDuration)
                return this.$router.push('/live-classes');

            axios.get('/api/live/search?category_id=' + this.filterCategory + '&duration=' + this.filterDuration + '&date=' + this.dateFormatted).then(response => {
                this.results = response.data;
                this.loading = false;
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
