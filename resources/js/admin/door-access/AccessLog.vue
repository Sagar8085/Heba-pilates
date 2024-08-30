<template>
<div>
    <PageHeader title="Door Access" subtitle="Access Log" icon="sensor_door">
        <!-- <button class="button">
            Generate Report
            <i class="material-icons">add_circle</i>
        </button> -->
    </PageHeader>

    <tab-list v-model="tab" :tabs="tabs" />

    <div class="page-content">
        <div class="table-list table-list--top">
            <div class="table-list__header">
                <h3><span v-if="pagination.from">{{ pagination.from + ' - ' + pagination.to }} of </span>{{ pagination.total }} Logs</h3>

                <div class="table-list__header-pagination">
                    <span :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === 1 || loading}" @click="change_page('previous')">navigate_before</span>
                    <span :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === pagination.last_page || loading}" @click="change_page('next')">navigate_next</span>
                </div>
            </div>

            <div class="table-list__scroll" v-if="!loading">
                <table class="table-list__table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Member ID</th>
                            <th>Gym</th>
                            <th>Membership</th>
                            <th>Time</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(log, index) in accessLog" :key="index">
                            <td><router-link :to="'/admin/members/' + log.user_id">{{ log.user.name }}</router-link></td>
                            <td>#{{ log.user_id }}</td>
                            <td v-if="log.gym !== null"><router-link :to="'/admin/gyms/' + log.gym_id">{{ log.gym.name }}</router-link></td>
                            <td v-else>Not Collected</td>
                            <td>{{ log.user.subscription ? log.user.subscription.name : 'No Membership' }}</td>
                            <td>{{ log.scanned_at_human }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-list__scroll" v-else>
                <table class="table-list__table">
                    <i class="fas fa-spinner fa-spin" style="margin: 1.5rem;"></i>
                </table>
            </div>
        </div>
    </div>
</div>
</template>


<script>
import axios from 'axios';
import PageHeader from '../layout/PageHeader.vue'
import TabList from '../layout/TabList.vue'
import FilterableDataTable from '../layout/FilterableDataTable.vue';
import ProfilePicture from '../../components/ProfilePicture.vue';

export default {
    components: { PageHeader, TabList, FilterableDataTable, ProfilePicture },

    props: {
        authUser: Object
    },

    mounted() {
        this.loadAccessLog();
    },

    data () {
        return {
            loading: true,
            accessLog: [],
            pagination: {},
            tab: 'log',
            tabs: [ { name: 'Live', key: 'live', link: '/admin/dashboard/door-access' }, { name: 'Access Log', key: 'log' } ],
            tableHeadings: {
                avatar: '',
                name: 'Name',
                member_id: 'Member ID',
                membership: 'Membership Type',
                // days_since_last_visit: 'Days Since Last Visit',
                // door: 'Door',
                scanned_at: 'Time & Date',
                actions: ''
            }
        }
    },

    methods: {
        loadAccessLog(page = 1) {
            this.loading = true;

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/door-access/all?page=' + page).then(response => {
                this.accessLog = response.data.data;
                this.pagination = response.data;
                this.loading = false;
            }).catch(error => {
                if(error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            });
        },


        change_page(type) {
            this.loading = true;

            if (type === 'previous') {
                this.loadAccessLog((this.pagination.current_page - 1));

            } else {
                if (this.pagination.current_page !== this.pagination.last_page) {
                    this.loadAccessLog((this.pagination.current_page + 1));
                }
            }
        },
    }
}
</script>
