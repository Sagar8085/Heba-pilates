<template>
    <aside v-if="isPanel" class="auth-subscription-panel fixed-panel fixed-panel--bottom">
        <div class="wrapper">
            <div class="auth-subscription-panel__info">
                <h4 class="auth-subscription-panel__info__title">{{ subscription.name }}</h4>
                <p class="auth-subscription-panel__info__description">{{ subscription.description }}</p>
                <button class="button button--link" @click="select">Change membership type</button>
            </div>
            <div class="auth-subscription-panel__pricing">
                <span class="auth-subscription-card__price">
                    £{{ subscription.price }}<small>.00</small>
                </span>
                <!-- <div> -->
                    <p class="page-subtitle">{{ paymentType }}</p>
                    <small class="auth-subscription-card__footer">{{ footer }}</small>
                <!-- </div> -->
            </div>
        </div>
    </aside>

    <div v-else class="auth-subscription-card">
        <div class="auth-subscription-card__header">
            <span class="auth-subscription-card__price">
                £{{ subscription.price }}<small>.00</small>
            </span>
            <span class="page-subtitle">{{ paymentType }}</span>
        </div>

        <h3 class="auth-subscription-card__title">{{ subscription.name }}</h3>

        <p class="auth-subscription-card__description">
            {{ subscription.description }}
        </p>

        <div v-if="subscription.studio_visits > 0" class="auth-subscription-card__visits">
            {{ subscription.studio_visits }} <small>Studio visits {{ subscription.visit_frequency }}</small>
        </div>

        <p v-else class="auth-subscription-card__visits">
            <small>Studio visits available on a PAYG basis</small>
        </p>

        <button class="button button--full" @click="select">
            Choose {{ subscription.name }}
        </button>

        <p class="auth-subscription-card__footer">
            £{{ subscription.price }}.00 {{ footer }}
        </p>
        
    </div>
</template>

<script>
export default {
    props: { subscription: Object, isCreditPack: Boolean, isPanel: Boolean },
    computed: {
        paymentType () {
            switch (this.subscription.payment_type) {
                case 'one-time':
                    return 'One-Time Payment'
                case 'monthly':
                    return 'Per Month'
                default: 
                    return ''
            }
        },
        footer () {
            switch (this.subscription.payment_type) {
                case 'one-time':
                    return this.isCreditPack 
                        ? 'One-Time Payment. Credits are valid for 3 months' 
                        : 'One-Time Payment'
                case 'monthly':
                    return 'Billed by monthly direct debit until cancelled'
                default: 
                    return ''
            }
        }
    },
    methods: {
        select () {
            this.$emit('select', this.subscription)
        }
    }
}
</script>