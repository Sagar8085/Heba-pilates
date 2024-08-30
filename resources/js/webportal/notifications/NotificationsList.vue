<template>
    <ul class="notifications" v-if="displayedNotifications.length > 0">
        <li
            v-for="(notification, i) in displayedNotifications"
            :key="notification.id"
            :class="{ 'notification': true, 'notification--seen': notification.read, 'notification--interactive': notification.link }">
            <small class="notification__date">{{ notification.date }}</small>
            <div class="notification__card" @click="goToLink(notification)">
                <div class="notification__card__content">
                    <div class="notification__header">
                        <span :class="iconClasses(notification.type)">
                            <i class="material-icons">{{ getIcon(notification.type) }}</i>
                        </span>
                        <strong class="notification__title">{{ notification.message }}</strong>
                    </div>
                    <p class="notification__description">{{ notification.time_human }}</p>
                </div>
                <div class="notification__menu-container">
                    <button
                        class="notification__menu-button button button--icon button--transparent"
                        @click.stop="toggleMenu(notification.id)">
                        <i class="fas fa-ellipsis-h" />
                    </button>
                    <div v-if="displayedMenuId == notification.id" class="notification__menu" v-on-clickaway="() => toggleMenu(null)">
                        <button class="button button--with-icon button--transparent" @click.stop="deleteNotification(i)">
                            <i class="fas fa-trash-alt" />
                            Delete notification
                        </button>

                        <button v-if="!notification.read" class="button button--with-icon button--transparent" @click.stop="markRead(i)">
                            <i class="fas fa-check-circle" />
                            Mark as read
                        </button>
                    </div>
                </div>
            </div>
        </li>
    </ul>

    <div class="notifications notifications--no-data" v-else>
        <div class="notification__card">You're all caught up!</div>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';

export default {
    props: {
        hasUnread: Boolean,
        maxLength: Number,
        notifications: Array
    },
    mixins: [ clickaway ],
    data () {
        return {
            displayedMenuId: null
        }
    },
    computed: {
        localHasUnread: {
            get () {
                return this.hasUnread;
            },
            set (value) {
                this.$emit('update:has-unread', value)
            }
        },
        unreadNotifications () {
            return this.notifications.filter(n => n.unread);
        },
        displayedNotifications () {
            return this.maxLength ?
                this.notifications.filter((n, i) => i < this.maxLength) :
                this.notifications;
        }
    },
    methods: {
        getNotifications () {
            // Get notifications from API

            this.localHasUnread = this.unreadNotifications.length > 0;
        },
        markAllAsRead () {
            if (this.unreadNotifications.length == 0) return;

            this.notifications.forEach(n => n.unread = false);
            this.localHasUnread = false;
            // Hook into API
        },
        toggleMenu (id) {
            this.displayedMenuId = this.displayedMenuId == id ? null : id;
        },
        deleteNotification (index) {
            this.notifications.splice(index, 1);
            this.displayedMenuId = null;
            // Hook into API
        },
        markRead (index) {
            this.notifications[index].unread = false;
            this.displayedMenuId = null;
            this.localHasUnread = this.unreadNotifications.length > 0;
            // Hook into API
        },
        getIcon (type) {
            switch (type) {
                case 'system':
                    return 'notifications';
                case 'session':
                    return 'calendar_today';
                case 'course':
                case 'class':
                    return 'video_library';
                case 'session':
                    return 'done';
                default:
                    return 'notifications'
            }
        },
        iconClasses (type) {
            return {
                'notification__header__icon': true,
                'notification__header__icon--blue': type == 'system',
                'notification__header__icon--green': type == 'course' || type == 'class',
                'notification__header__icon--red': type == 'session'
            };
        },
        goToLink (notification) {
            if (!notification.link) return;

            this.$router.push(notification.link);
        }
    },
    created () {
        this.getNotifications();
    }

}
</script>
