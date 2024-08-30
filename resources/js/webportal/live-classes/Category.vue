<template>
    <div class="live-classes">
        <div v-if="!loading" class="page-content">
            <div class="wrapper">
                <breadcrumbs :links="breadcrumbLinks" />

                <LiveClassFilters
                    :date.sync="filterDate"
                    :category.sync="filterCategory"
                    :categories="categories"
                    :duration.sync="filterDuration" />
            </div>

            <VideoList
                :title="this.category.name"
                :videos="this.category.upcoming_classes"
                :loading="loading"
                :isLive="true"
                :verticalScroll="true"
                />
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VideoList from '../layout/VideoList.vue'
import FullscreenVideo from '../../components/FullscreenVideo.vue'
import Breadcrumbs from '../../components/Breadcrumbs.vue'
import LiveClassFilters from '../../components/LiveClassFilters.vue'

export default {
    components: {
        VideoList,
        FullscreenVideo,
        Breadcrumbs,
        LiveClassFilters
    },

    props: {
        authUser: Object
    },

    mounted() {
    },

    data () {
        return {
            loading: true,
            categories: [],
            category: {
                upcoming_classes: []
            },
            filterDate: '',
            filterCategory: '',
            filterDuration: ''
        };
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

    computed: {
        breadcrumbLinks () {
            return [
                { title: 'Live Classes', link: '/live-classes' },
                { title: this.category.name }
            ]
        }
    },

    methods: {
        loadCategory() {
            axios.get('/api/live/category/' + this.$route.params.slug).then(response => {
                this.category = response.data;
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
    },
    beforeMount() {
        this.loadCategory();
        this.loadCategories();
    }
};
</script>
