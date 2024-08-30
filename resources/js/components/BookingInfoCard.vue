<template>
    <div class="booking-card booking-card--large">
        <section class="booking-card__header">
            <h1 v-if="status" :class="`booking-card__header__title booking-card__status--${status.toLowerCase()}`">
                {{ status }}
            </h1>
            <p class="booking-card__header__subtitle">
                <template v-if="type == 'package'">
                    {{ packageInfo.sessions.length + '/' + packageInfo.sessionAmount }}
                    <span>
                        {{ packageInfo.sessions.length == 1 ? 'Session' : 'Sessions' }} booked
                    </span>
                </template>
                <template v-else>
                    {{ datetime }}
                </template>
            </p>
            <dl class="booking-card__header__list">
                <div v-if="packageInfo" class="booking-card__header__list__item">
                    <dt>Package ID</dt>
                    <dd>{{ packageInfo.id }}</dd>
                </div>
                <div v-if="type == 'session' && sessionId" class="booking-card__header__list__item">
                    <dt>Session ID</dt>
                    <dd>{{ sessionId }}</dd>
                </div>
                <div v-if="bookingId" class="booking-card__header__list__item">
                    <dt>Booking ID</dt>
                    <dd>{{ bookingId }}</dd>
                </div>
                <div v-if="equipmentName" class="booking-card__header__list__item">
                    <dt>Equipment</dt>
                    <dd>{{ equipmentName }}</dd>
                </div>
                <div v-if="duration" class="booking-card__header__list__item">
                    <dt>Duration</dt>
                    <dd>{{ duration }} minutes</dd>
                </div>
                <div v-if="rating" class="booking-card__header__list__item">
                    <dt>Rating</dt>
                    <dd>{{ rating }} / 5</dd>
                </div>
            </dl>
        </section>
        <section class="booking-card__content">
            <section class="booking-card__section booking-card__section--row">
                <div v-if="trainerInfo" class="booking-card__trainer">
                    <img v-if="trainerInfo.profile" class="booking-card__image booking-card__image--small" :src="trainerInfo.profile.avatar" alt="Trainer image" />
                    <p class="booking-card__trainer__content">
                        <strong>{{ trainerInfo.name }}</strong>
                        <span>Virtual Coach</span>
                    </p>
                </div>
                <div v-if="memberInfo" class="booking-card__trainer">
                    <p class="booking-card__trainer__content">
                        <strong>{{ memberInfo.name }}</strong>
                        <span>{{ memberInfo.email }}</span>
                    </p>
                </div>
            </section>

            <!-- custom sections -->
            <slot></slot>

            <section v-if="historyInfo" class="booking-card__section">
                <h2 class="booking-card__section__title">History</h2>
                <ul class="booking-card__history">
                    <li
                        v-for="(item, index) in sortedHistory"
                        :key="index"
                        class="booking-card__history__item">
                        <span>{{ item.message }}</span>
                        <span class="booking-card__history__item__subtitle">{{ formatHistoryDate(item.date) }}</span>
                    </li>
                </ul>
            </section>
        </section>
    </div>
</template>

<script>
export default {
    props: {
        sessionId: Number,
        status: String,
        datetime: String,
        duration: Number,
        rating: Number,
        type: {
            type: String,
            default: 'session' // package || session
        },
        packageInfo: Object,
        trainerInfo: Object,
        memberInfo: Object,
        historyInfo: Array,
        equipmentName: String,
        bookingId: [Number,String]
    },
    computed: {
        sortedHistory () {
            return this.historyInfo ?
                this.historyInfo.sort((a, b) => new Date(b.date) - new Date(a.date))
                : [];
        }
    },
    methods: {
        formatHistoryDate (date) {
            return moment(date).format('MMM D, YYYY • HH:mm');
        }
    }
}
</script>