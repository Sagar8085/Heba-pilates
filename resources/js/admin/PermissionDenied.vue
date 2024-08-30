<template>
    <div>
        <section class="forbidden forbidden--full">
            <div>
                <img src="/images/illustrations/questions.svg">
                <h2>Access Denied</h2>
                <p>You do not have permission to view this resource, if you require access to this please contact your Heba administrator.</p>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
         authUser: Object
    },

    data() {
        return {
        }
    },

    mounted() {
    },

    methods: {
    }
}
</script>
