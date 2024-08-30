<template>
    <div>
        <div class="modal__box modal__box--small modal__body">
            <h2 class="modal__top modal__title">Delete {{ element }}</h2>

            <p>Are you sure you want to delete this {{ element }}?</p>

            <div class="modal__buttons">
                <button class="button button--outline" @click="$emit('close')">Cancel</button>
                <button class="button button--red" @click="Delete()">Delete</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {

    components: {   },

    props: {
        element: String,
        url: String,
    },

    data() {
        return {

        }
    },
    methods: {
        Delete() {
            axios.post(this.url).then(response => {
                this.$emit('complete');
            })
        },
    }
}
</script>
