<template>
<div class="page-content wrapper help help--articles">
    <breadcrumbs :links="breadcrumbLinks" />

    <h1>The Full Heba Pilates Guide</h1>

    <!-- <div class="row">
        <div v-for="(category, index) in categories" :key="index" class="columns six-sm three-lg">
            <router-link :to="'/help/topics/' + category.slug" class="help__topic">
                <h3 class="help__topic-title">{{ category.name }}</h3>
            </router-link>
        </div>
    </div> -->

    <section class="help__section">
        <div class="row">
            <div v-for="(category, index) in categories" :key="index" class="columns six-sm three-lg">
                <div class="help__section__topic">
                    <h3 class="help__section__topic__title">{{ category.name }}</h3>

                    <router-link
                        v-for="(article, i) in category.articles"
                        :key="i"
                        class="help__section__topic__link"
                        :to="'/help/articles/' + article.slug">
                        {{ article.name }}
                    </router-link>
                </div>
            </div>
        </div>
    </section>
</div>
</template>

<script>
import Breadcrumbs from '../../components/Breadcrumbs.vue'
export default {
    components: { Breadcrumbs },

    mounted() {
        this.loadCategories();
    },

    data () {
        return {
            breadcrumbLinks: [
                { link: '/help', title: 'Help Centre' },
                { title: 'All Help Topics' }
            ],

            categories: []
        }
    },

    methods: {
        loadCategories() {
            axios.get('/api/help/categories').then(response => {
                this.categories = response.data;
            });
        }
    }
}
</script>
