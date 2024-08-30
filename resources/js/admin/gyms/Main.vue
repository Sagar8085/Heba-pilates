<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--member">
                            <i class="material-icons">assignment_ind</i>
                        </div>
                        Gyms
                    </h1>

                    <h2 class="page-header__sub">My Gyms</h2>
                </div>

                <!-- <div class="page-header__col">
                    <button @click="displayCreateModal = true" class="button">Create Member <i class="material-icons">add_circle</i></button>
                </div> -->
            </div>
        </section>

        <section>
            <div v-if="gyms.length > 0" class="page-content page-content--small-top">
                <div class="table-list table-list--top">
                    <div class="table-list__header">
                        <h3>{{ gyms.length }} Gyms</h3>
                    </div>

                    <div class="table-list__scroll">
                        <table class="table-list__table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Contact Number</th>
                                    <th>Contact Email</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(gym, index) in gyms" :key="index">
                                    <td><router-link :to="'/admin/gyms/' + gym.id">{{ gym.name }}</router-link></td>
                                    <td>{{ gym.address }}</td>
                                    <td>{{ gym.phone_number }}</td>
                                    <td>{{ gym.email }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            loading: true,
            gyms: []
        }
    },

    mounted() {
        this.loadGyms();
    },

    methods: {
        /*
         * Load all of this users accessable gyms.
         * @param {none}
         * @return {json}
         */
        loadGyms() {
            axios.get('/api/admin/gyms').then(response => {
                this.gyms = response.data;
            })
            .catch(error => {
                console.log('Unable to load your gyms at this time.')
            }).finally(() => this.loading = false);
        }
    }
}
</script>
