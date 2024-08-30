<template>
    <li :class="{ header__account__notifications: true, 'header__account__notifications--new': hasUnread, active: this.$route.path.includes('/notifications') }">
        <a @click.stop="toggleNotifications">
            <i class="header__account__image material-icons">notifications</i>
            <span class="header__account__text">Notifications</span>
        </a>
        <transition name="notifications">
            <aside v-show="showNotifications" class="notifications-popup" v-on-clickaway="close">
                <button
                    class="notifications-popup__close button button--transparent button--icon button"
                    @click="close">
                    <i class="fas fa-times" />
                </button>
                <strong class="notifications-popup__title">Notifications</strong>
                <notifications-list ref="notifications" :has-unread.sync="hasUnread" :max-length="3" :notifications="notifications" />
                <div class="notifications-popup__footer">
                    <button class="button button--plain button--transparent" :disabled="!hasUnread" @click="markAllAsRead">Mark all as read</button>
                    <router-link class="button button--plain button--transparent" to="/notifications">View all</router-link>
                </div>
            </aside>
        </transition>
    </li>
</template>

<script>
import NotificationsList from './NotificationsList.vue'
import { mixin as clickaway } from 'vue-clickaway';

export default {
    components: {
        NotificationsList
    },
    mixins: [ clickaway ],
    data () {
        return {
            hasUnread: false,
            smallScreen: false,
            showNotifications: false
        }
    },
    props: {
        notifications: Array
    },
    methods: {
        markAllAsRead () {
            this.$refs.notifications.markAllAsRead();
        },
        async toggleNotifications () {
            this.checkScreenSize();

            await this.$nextTick();

            this.smallScreen ?
                this.$router.push('/notifications') :
                this.showNotifications = !this.showNotifications;

            if (this.showNotifications) this.$emit('show');
        },
        close () {
            this.showNotifications = false;
        },
        checkScreenSize () {
            this.smallScreen = document.body.offsetWidth < 992;

            if (this.smallScreen) this.close();
        }
    },
    mounted () {
        this.checkScreenSize()
    }
}
</script>
