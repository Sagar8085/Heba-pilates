<template>
<div class="page-content wrapper help help--articles">
    <breadcrumbs :links="breadcrumbLinks" />

    <h1>The Full Heba Pilates Guide</h1>

    <div class="help__articles row">
        <div v-for="article in category.articles" :key="article.id" class="columns six-md three-xl">
            <router-link class="article-card" :to="'/help/articles/' + article.slug">
                <h3 class="article-card__title">{{ article.name }}</h3>
                <p class="article-card__description">{{ article.excerpt }}</p>
                <router-link class="button button--transparent button--plain" :to="'/help/articles/' + article.slug">
                    Read article
                    <i class="material-icons">keyboard_arrow_right</i>
                </router-link>
            </router-link>
        </div>
    </div>
</div>
</template>

<script>
import Breadcrumbs from '../../components/Breadcrumbs.vue'
export default {
    components: { Breadcrumbs },

    mounted() {
        this.loadCategory();
    },

    data () {
        return {
            category: {},

            breadcrumbLinks: [
                { link: '/help', title: 'Help Centre' }
            ],
        }
    },

    methods: {
        loadCategory() {
            console.log('loading single category')
            axios.get('/api/help/category/' + this.$route.params.slug).then(response => {
                this.category = response.data;

                this.breadcrumbLinks.push({ link: '/help/topics/' + this.$route.params.slug, title: this.category.name });
            });
        }
    }
}
</script>
