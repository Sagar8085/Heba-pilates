<template>
    <div class="notifications-page page-content">
        <div class="wrapper">
            <header class="notifications-page__header">
                <h1>Notifications</h1>
                <button class="button button--plain button--transparent" :disabled="!hasUnread" @click="markAllAsRead">Mark all as read</button>
            </header>
            <notifications-list ref="notifications" :has-unread.sync="hasUnread" :notifications="notifications" />
        </div>
    </div>
</template>

<script>
import NotificationsList from './NotificationsList.vue';

export default {
    components: {
        NotificationsList
    },
    data () {
        return {
            hasUnread: false,
            notifications: []
        }
    },
    mounted () {
        this.loadNotifications();
    },
    methods: {
        markAllAsRead () {
            this.$refs.notifications.markAllAsRead();
        },
        loadNotifications() {
            axios.get('/api/account/notifications').then(response => {
                this.notifications = response.data;
            })
            .catch(error => {
                // console.log('ERROR');
            });
        },
    }
}
</script>
