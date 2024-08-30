<template>
    <div class="help page-content wrapper">
        <h1>What can we help you with?</h1>

        <search-input @search="submitSearch" class="help__search" placeholder="Search articles" />

        <h2 v-if="articles.length > 0" class="help__subtitle">Popular help topics</h2>

        <div v-if="articles.length > 0" class="help__articles row">
            <div v-for="article in articles" :key="article.id" class="columns six-md three-xl">
                <router-link class="article-card" :to="'/help/articles/' + article.slug">
                    <h3 class="article-card__title">{{ article.name }}</h3>
                    <p class="article-card__description">{{ article.category.name }}</p>
                    <router-link class="button button--transparent button--plain" :to="'/help/articles/' + article.slug">
                        Read article
                        <i class="material-icons">keyboard_arrow_right</i>
                    </router-link>
                </router-link>
            </div>
        </div>

        <h2 class="help__subtitle help__subtitle--light">Can't see what you're looking for? <router-link to="/help/topics">View all help topics</router-link></h2>
        <h2 class="help__subtitle help__subtitle--light help__subtitle--small">Want to speak to someone? <router-link to="/contact">Submit Support Request</router-link></h2>

    </div>
</template>

<script>
import SearchInput from '../../components/SearchInput.vue'
export default {
    components: { SearchInput },

    data () {
        return {
            articles: []
        }
    },

    mounted() {
        this.loadArticles();
    },

    methods: {
        loadArticles() {
            axios.get('/api/help/featured').then(response => {
                this.articles = response.data;
            })
        },

        submitSearch(value) {
            this.$router.push('/help/search?term=' + value);
        }
    }
}
</script>
