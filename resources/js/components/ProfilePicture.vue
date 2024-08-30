<template>
    <div :class="classes" @click="$emit('click')">
        <img v-if="image" :src="image" :alt="altText" />
        <template v-else>
            <template v-if="firstName">{{ firstName[0] }}</template><template v-if="lastName">{{ lastName[0] }}</template>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        firstName: String,
        lastName: String,
        size: String,
        image: String,
        altText: {
            type: String,
            default: 'Profile Picture'
        },
        interactive: Boolean
    },
    computed: {
        classes () {
            return {
                'profile-picture': true,
                'profile-picture--large': this.size == 'large',
                'profile-picture--xlarge': this.size == 'xlarge',
                'profile-picture--initials': !this.image,
                'profile-picture--interactive': this.interactive
            }
        }
    }
}
</script>