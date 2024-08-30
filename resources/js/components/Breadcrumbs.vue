<template>
    <ul class="breadcrumbs">
        <slot>
            <li v-for="(link, i) in links" :key="i" class="breadcrumbs__item">
                <router-link v-if="getLink(i)" class="breadcrumbs__item__link" :to="getLink(i)">{{ link.title }}</router-link>
                <span v-else class="breadcrumbs__item__link" @click="$emit('click', i)">{{ link.title }}</span>
            </li>
        </slot>
    </ul>
</template>

<script>
export default {
    props: { links: Array },
    methods: {
        getLink (index) {
            return index == this.links.length - 1 && !this.links[index].link
                ? this.$route.path
                : this.links[index].link;
        }
    }
}
</script>