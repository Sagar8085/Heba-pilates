<template>
    <div class="live-classes">
        <div v-if="!loading" class="page-content">
            <div class="wrapper">
                <breadcrumbs :links="breadcrumbLinks" />
                <section class="filter-bar">
                    <form-input v-model="filter.date" type="calendar" placeholder="Date" />

                    <form-input v-model="filter.category" type="select" placeholder="Category">
                        <option value="category1">Category 1</option>
                        <option value="category2">Category 2</option>
                    </form-input>

                    <form-input v-model="filter.duration" type="select" placeholder="Duration">
                        <option value="30">30 minutes</option>
                        <option value="60">60 minutes</option>
                        <option value="120">120 minutes</option>
                    </form-input>
                </section>
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
import FormInput from '../../components/FormInput.vue'

export default {
    components: {
        VideoList,
        FullscreenVideo,
        Breadcrumbs,
        FormInput
    },

    props: {
        authUser: Object
    },

    mounted() {
    },

    data () {
        return {
            loading: true,
            category: {
                upcoming_classes: []
            },
            filter: {
                date: null,
                category: '',
                duration: ''
            }
        };
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
        }
    },
    beforeMount() {
        this.loadCategory();
    }
};
</script>
