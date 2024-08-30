<template>
    <ul class="activity-list">
        <li     
            v-for="(item, index) in list"
            :key="index"
            class="activity-list__item">
            <div :class="iconClasses(item.colour)">
                <i :class="`fas fa-${item.icon}`" />
            </div>
            <div class="activity-list__item__content">
                <p class="activity-list__item__title">{{ item.title }}</p>
                <span class="activity-list__item__subtitle">{{ item.subtitle }}</span>
            </div>
            <div class="activity-list__item__right">
                <span class="activity-list__item__date">{{ item.date }}</span>
                <button 
                    v-if="item.joinButton"
                    class="button button--plain button--transparent"
                    @click="$emit('join', index)">
                    Join Now
                </button>
            </div>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        // array of { title, subtitle, icon, date, colour } objects
        list: Array
    },
    methods: {
        iconClasses (colour) {
            return colour ? 
                `activity-list__item__icon activity-list__item__icon--${colour}` :
                'activity-list__item__icon'
        }
    }
}
</script>