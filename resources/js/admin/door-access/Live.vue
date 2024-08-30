<template>
<div>
    <PageHeader title="Door Access" subtitle="Live" icon="sensor_door" />

    <tab-list v-model="tab" :tabs="tabs" />

    <div class="page-content">
        <h3 class="page__title--secondary">Most Recent</h3>

        <div class="door-access__list" v-if="latestArrival.length > 0">
            <door-access-card v-for="record in latestArrival" :key="record.id" :record="record" />
        </div>

        <h3 class="page__title--secondary">Previous</h3>

        <div class="door-access__list" v-if="accessLog.length > 0">
            <door-access-card v-for="record in accessLog" :key="record.id" :record="record" />
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios'
import PageHeader from '../layout/PageHeader.vue'
import Datepicker from 'vuejs-datepicker';
import Echo from 'laravel-echo';
import TabList from '../layout/TabList.vue'
import DoorAccessCard from './DoorAccessCard.vue'

export default {
    components: { PageHeader, Datepicker, TabList, DoorAccessCard },

    props: {
        authUser: Object
    },

    data () {
        return {
            accessLog: [
                {
                    id: 1,
                    member: {
                        avatar: null,
                        id: 1,
                        first_name: 'Sophie',
                        last_name: 'Popter',
                        name: 'Sophie Popter',
                        membership: 'Mainly Studio'
                    },
                    days_since_last_visit: 5,
                    door: 'Main Entrance',
                    scanned_at: '12:34'
                },
                {
                    id: 2,
                    member: {
                        avatar: null,
                        id: 1,
                        first_name: 'Sophie',
                        last_name: 'Popter',
                        name: 'Sophie Popter',
                        membership: 'Mainly Studio'
                    },
                    days_since_last_visit: 5,
                    door: 'Main Entrance',
                    scanned_at: '12:34'
                },
                {
                    id: 3,
                    member: {
                        avatar: null,
                        id: 1,
                        first_name: 'Sophie',
                        last_name: 'Popter',
                        name: 'Sophie Popter',
                        membership: 'Mainly Studio'
                    },
                    days_since_last_visit: 5,
                    door: 'Main Entrance',
                    scanned_at: '12:34'
                }
            ],
            latestArrival: [
                {
                    id: 1,
                    member: {
                        avatar: null,
                        id: 1,
                        first_name: 'Sophie',
                        last_name: 'Popter',
                        name: 'Sophie Popter',
                        membership: 'Mainly Studio'
                    },
                    days_since_last_visit: 5,
                    door: 'Main Entrance',
                    scanned_at: '12:34'
                }
            ],
            tab: 'live',
            tabs: [ { name: 'Live', key: 'live' }, { name: 'Access Log', link: '/admin/dashboard/door-access/log', key: 'log' } ]
        }
    },

    beforeDestroy() {
        window.Echo.disconnect();
    },

    mounted() {
        this.loadAccessLog();

        window.Pusher = require('pusher-js');

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '5356ced04d71af946e6d',
            cluster: 'eu',
            forceTLS: true,
            auth: {
                headers: {
                    Authorization: 'Bearer Xo7frxJBZzoAKNSdyNUvoRBZhKpfCypD3NXe9fZP2sfRIpuUzzc0IRufoNe569aSr9mnc7Uf2OUfWRTr0zNSIXBPDzKcL60xpn4FmI88PFP8Fei7mF6J9IkvixU5CknLjXSefB0tnQ8DFUrWk9gujhdfc60ca8c4904562d8e104faf771c150f337af16'
                },
            },
        });

        window.Echo.private('doorAccessLive').listen('.new-arrival', (e) => {
            this.accessLog.unshift(e[0]);
            this.latestArrival = [e[0]];
        });
    },

    methods: {
        loadAccessLog() {
            axios.get('/api/admin/door-access/live').then(response => {
                this.accessLog = response.data.all;
                this.latestArrival = response.data.latest;
            }).catch(error => {
                if(error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            });
        }
    }
}
</script>
