<template>
    <form @submit.prevent="destroy">
        <button
            type="submit"
            class="button button--red"
            v-html="text"
        ></button>
    </form>
</template>

<script>
import axios from "axios";

export default {
    props: {
        url: String,
        text: {
            type: String,
            default: 'Delete'
        }
    },

    methods: {
        destroy() {
            axios
                .delete(this.url)
                .then(() => this.$emit('success'))
                .catch(() => this.$emit('error'));
        }
    }
}
</script>