<template>
    <div class="page-content wrapper help help--article">

        <loading-spinner :loading="loading" loadingText="help article" :noData="!article.id">
            <template slot="no-data">
                <p>Article not found.</p>
                <router-link class="button" to="/help">Back to Help Centre</router-link>
            </template>
        </loading-spinner>

        <template v-if="!loading && article.id">
            <breadcrumbs :links="breadcrumbLinks" />

            <div class="row">
                <aside class="columns three-xl">
                    <h2 class="help__subtitle help__subtitle--left">Similar help topics</h2>
                    <ul class="help__similar-topics">
                        <li v-for="topic in similar" :key="topic.slug">
                            <router-link :to="'/help/articles/' + topic.slug">
                                {{ topic.name }}
                            </router-link>
                        </li>
                    </ul>
                </aside>
                <article class="columns nine-md six-xl">
                    <h1>{{ article.name }}</h1>
                    <div v-html="article.content" class="help-article"></div>
                </article>
            </div>
        </template>
    </div>
</template>

<script>
import Breadcrumbs from '../../components/Breadcrumbs.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'
export default {
    components: { Breadcrumbs, LoadingSpinner },

    data () {
        return {
            article: {},
            similar: [],
            loading: true
        }
    },

    computed: {
        breadcrumbLinks () {
            return [
                { link: '/help', title: 'Help Centre' },
                { link: '/help/topics/' + this.article.category.slug, title: this.article.category.name },
                { title: this.article.name }
            ]
        }
    },

    watch: {
        '$route.params.slug': function (id) {
            this.getArticle();
        }
    },

    methods: {
        getArticle () {
            // Hook into API
            axios.get('/api/help/article/' + this.$route.params.slug).then(response => {
                this.article = response.data.article;
                this.similar = response.data.similar;
                this.loading = false;
            });
        }
    },
    mounted () {
        this.getArticle();
    }
}
</script>
