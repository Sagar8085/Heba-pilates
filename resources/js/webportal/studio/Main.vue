<template>
    <div class="wrapper page-content studio">
        <h1>Select a studio to reserve</h1>
        <div class="row">
            <div class="columns six-lg four-xl">
                <!-- <form-input v-model="searchTerm" class="studio__search" placeholder="Search town. city or postcode">
                    <i slot="after-input" class="form-input__after material-icons">search</i>
                </form-input> -->

                <loading-spinner
                    :loading="loading"
                    loadingText="nearby studios"
                    :noData="studios.length == 0"
                    noDataText="We couldn't find any Heba Pilates studios based around your search location." />

                <template v-if="!loading && studios.length > 0">
                    <p><strong>{{ studios.length }}</strong> Heba Pilates {{ studios.length == 1 ? 'studio' : 'studios' }} near you</p>

                    <div v-if="studios.length > 0" class="card-list">
                        <div v-for="studio in studios" :key="studio.id" class="card card--padded">
                            <h3 class="card__title">{{ studio.name }}</h3>

                            <p class="card__description">
                                {{ studio.address }}
                            </p>
                            <p class="card__description">Tel: {{ studio.phone_number }}</p>
                            <p class="card__description">Email: {{ studio.email }}</p>

                            <div class="card__footer">
                                <router-link class="button button--full" :to="'/studio/' + studio.id">Select</router-link>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="columns six-lg eight-xl">
                <google-map class="studio__map--large" :markers="mapMarkers" v-if="!loading" />
            </div>
        </div>
    </div>
</template>

<script>
import FormInput from '../../components/FormInput.vue'
import GoogleMap from '../../components/GoogleMap.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'

export default {
    components: { FormInput, LoadingSpinner, GoogleMap },

    mounted() {
        this.loadStudios();
    },

    data () {
        return {
            searchTerm: '',
            studios: [],
            loading: true
        }
    },

    computed: {
        mapMarkers () {
            return this.studios.map(x => x.position)
        }
    },

    methods: {
        loadStudios () {
            this.loading = true;

            axios.get('/api/gyms').then(response => {
                this.studios = response.data;
                this.loading = false;
            });
        }
    }
}
</script>
