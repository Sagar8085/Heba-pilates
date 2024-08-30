<template>
<div :class="{ 'subscription-card': true, 'subscription-card--active': active }">
    <section class="subscription-card__header">
        <h2 class="subscription-card__title">{{ title }}</h2>
        <p class="subscription-card__subtitle">
            <template v-if="price">
                {{ price }}
            </template>
            <template v-else>
                FREE Membership
            </template>
        </p>
        <p v-if="price" class="subscription-card__subtitle--small">Billed {{ billingPeriod }}</p>
    </section>
    <section class="subscription-card__content">
        <p class="subscription-card__list-title">What's included?</p>
        <ul class="subscription-card__list">
            <slot>
                <li v-for="(item, i) in benefits" :key="i">{{ item }}</li>
            </slot>
        </ul>
    </section>
    <section v-if="active || showUpgrade" class="subscription-card__footer">
        <button v-if="showUpgrade && !upgrading" class="button button--full" @click="upgrade">Purchase {{ title }}</button>
        <button v-if="showUpgrade && upgrading" class="button button--full"><i class="fas fa-spinner fa-spin"></i></button>
        <p v-if="active && showCurrent">Current Plan</p>
    </section>
    <section v-if="comingSoon" class="subscription-card__footer">
        <p>COMING SOON</p>
    </section>
</div>
</template>

<script>
export default {
    props: {
        title: String,
        price: String,
        active: Boolean,
        benefits: Array,
        showCurrent: Boolean,
        showUpgrade: Boolean,
        upgrading: Boolean,
        comingSoon: Boolean,
        billingPeriod: String
    },
    methods: {
        upgrade () {
            this.$emit('upgrade');
        }
    }
}
</script>
