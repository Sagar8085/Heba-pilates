<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">remember_me</i>
                        </div>
                        Sessions
                    </h1>

                    <h2 class="page-header__sub">All Sessions</h2>
                </div>
            </div>
        </section>

        <section class="page-content page-content--small-top">
            <filterable-data-table
                :cols="sessionListHeadings"
                :rows="sessions"
                :pagination="pagination"
                title="Sessions"
                searchPlaceholder="Search Sessions"
                v-on:nextPage="loadSessions((pagination.current_page + 1))"
                v-on:previousPage="loadSessions((pagination.current_page - 1))">

                <template v-slot:cell-name="{ item, cell }">
                    <router-link :to="'/admin/sessions/' + item.id">
                        {{ cell ? cell : 'Session #' + item.id }}
                    </router-link>
                </template>

                <!-- dummy data to be removed -->
                <template v-slot:cell-package="{ item, cell }">
                    Package Name
                </template>
                <!-- end dummy data -->

                <template v-slot:cell-member="{ item }">
                    <router-link :to="'/admin/members/' + item.member_id">{{ item.member.name }}</router-link>
                </template>

                <template v-slot:cell-trainer="{ item }">
                    <router-link :to="'/admin/trainers/' + item.trainer_id">{{ item.trainer.name }}</router-link>
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
            sessions: [],
            pagination: {},

            sessionListHeadings: {
                name: 'Session Name',
                package: 'Package Name',
                member: 'Member',
                trainer: 'Trainer',
                status_human: 'Status',
                time_human: 'Date/Time'
            }
        }
    },

    mounted() {
        this.loadSessions();
    },

    methods: {
        loadSessions(page) {
            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/sessions?page=' + page).then(response => {
                this.sessions = response.data.data;
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
