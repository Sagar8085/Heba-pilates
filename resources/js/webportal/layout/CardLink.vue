<template>
    <router-link
        :class="{
            'card-link': true,
            'card-link--plain': !image || isPlain
        }"
        :to="link"
        :style="image && !isPlain ? `background-image: url(${image})` : ''">
        <img v-if="isPlain && image" :src="image" class="card-link__bg" />
        <div v-if="tag" class="card-link__tag-container">
            <label :class="{
                'card-link__tag': true,
                'card-link__tag--solid': tag === 'PURCHASED',
                'card-link__tag--alt': (tag !== 'PURCHASED' && tag !== 'FREE')
            }">
                {{ tag }}
            </label>
        </div>
        <div class="card-link__text-container">
            <div class="card-link__title">
                {{ title }}
            </div>
            <slot></slot>
        </div>
    </router-link>
</template>

<script>
export default {
    props: {
        link: String,
        image: String,
        tag: String,
        tagType: String,
        title: String,
        isPlain: Boolean,
        purchase: Object
    }
}
</script>
