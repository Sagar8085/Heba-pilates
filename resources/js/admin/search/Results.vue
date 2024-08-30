<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        Search
                    </h1>

                    <h2 class="page-header__sub">{{ this.$route.query.q }}</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <filterable-data-table
                v-if="!loading"
                :cols="sessionListHeadings"
                :rows="results"
                :filterOptions="sessionFilters"
                title="Users"
                :enableSearch="false"
                :pagination="pagination"
                v-on:nextPage="loadResults((pagination.current_page + 1))"
                v-on:previousPage="loadResults((pagination.current_page - 1))">

                <template v-slot:cell-member="{ item, cell }">
                    <router-link v-if="item.role_id === 1 || item.role_id === 2" :to="'/admin/admins/' + item.id">{{ item.name }}</router-link>
                    <router-link v-if="item.role_id === 3" :to="'/admin/trainers/' + item.id">{{ item.name }}</router-link>
                    <router-link v-if="item.role_id === 4" :to="'/admin/members/' + item.id">{{ item.name }}</router-link>
                </template>

                <template v-slot:cell-type="{ item, cell }">
                    {{ item.role_id === 4 ? 'Member' : '' }}
                    {{ item.role_id === 3 ? 'Trainer' : '' }}
                    {{ item.role_id === 1 || item.role_id === 2 ? 'Administrator' : '' }}
                </template>

                <template v-slot:cell-email="{ item, cell }">
                    {{ item.email }}
                </template>

                <template v-slot:cell-phoneNumber="{ item, cell }">
                    {{ item.phone_number }}
                </template>

                <template v-slot:cell-gender="{ item, cell }">
                    {{ item.gender }}
                </template>

                <template v-slot:cell-age="{ item, cell }">
                    {{ item.age }}
                </template>

                <template v-slot:cell-actions="{ item }">
                    <router-link class="button button--icon button--transparent" :to="'/admin/sessions/' + item.id">
                        <i class="fas fa-arrow-right"></i>
                    </router-link>
                </template>

            </filterable-data-table>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import FilterableDataTable from '../layout/FilterableDataTable.vue'

export default {
    components: { FilterableDataTable },

    data() {
        return {
            loading: true,
            pagination: {},
            results: [],

            sessionListHeadings: {
                member: 'Name',
                type: 'Type',
                email: 'Email Address',
                phoneNumber: 'Phone Number',
                gender: 'Gender',
                age: 'Age'
            },

            sessionFilters: [
                { option: 'Recently registered', value: 'recent' }
            ],
        }
    },

    mounted() {
        this.loadResults();
    },

    watch: {
        '$route.query.q': function (id) {
            this.loadResults();
        }
    },

    methods: {
        loadResults(page) {
            console.log('loading results:');

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/search?page=' + page + '&q=' + this.$route.query.q).then(response => {
                this.results = response.data.data;
                this.pagination = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        }
    }
}
</script>
