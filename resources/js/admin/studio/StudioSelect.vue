<template>
<div>
    <PageHeader title="Studio Reservations" subtitle="All Studios" icon="event" />
    <div class="page-content">
        <div class="row">
            <div
                v-for="studio in gyms"
                :key="studio.id"
                class="columns six-md four-lg">
                <router-link :to="'/admin/studio/' + studio.id" class="card--basic">
                <img class="card__image" :src="studio.image" :alt="studio.name" />
                <h4 class="card__title">{{ studio.name }}</h4>
                <p>
                    <span>{{ studio.address }}</span>
                </p>

                <p>Call: {{ studio.phone_number }}</p>

                </router-link>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios'
import PageHeader from '../layout/PageHeader.vue'

export default {
    components: { PageHeader },

    data () {
        return {
            gyms: []
        }
    },

    mounted() {
        this.loadGyms();
    },

    methods: {
        loadGyms() {
            axios.get('/api/gyms').then(response => {
                this.gyms = response.data;
            });
        }
    }
}
</script>
