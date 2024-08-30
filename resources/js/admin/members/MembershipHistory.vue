<template>
<div>
    <PageHeader title="Member Profile" :subtitle="'Member #' + this.$route.params.id + ' - Membership History'" icon="assignment_ind" />

    <section class="back">
        <router-link :to="'/admin/members/' + this.$route.params.id">
            <img src="/images/icons/backblue.png" alt="Back"> &nbsp; Back to Member Profile
        </router-link>
    </section>

    <div class="page-content">
        <div class="table-list">
            <div class="table-list__header">
                <h3>{{ memberships.length }} Memberships</h3>
            </div>

            <div class="table-list__scroll">
                <table class="table-list__table">
                    <thead>
                        <tr>
                            <th>Tier</th>
                            <th>Online Credits</th>
                            <th>Studio Credits</th>
                            <th>Expires</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(membership, index) in memberships" :key="index">
                            <td>{{ membership.name }}</td>
                            <td>{{ membership.online_credits_human }}</td>
                            <td>{{ membership.studio_credits_human }}</td>
                            <td>{{ membership.expires_human }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios'
import PageHeader from '../layout/PageHeader.vue'
import Datepicker from 'vuejs-datepicker';

export default {
    components: { PageHeader, Datepicker },

    data () {
        return {
            memberships: [],
            loading: true
        }
    },

    mounted() {
        this.loadMemberships();
    },

    methods: {
        loadMemberships() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/memberships/all').then(response => {
                this.memberships = response.data;
            });
        }
    }
}
</script>
