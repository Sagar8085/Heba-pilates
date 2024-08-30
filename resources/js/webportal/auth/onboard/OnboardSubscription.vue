<template>
    <div class="onboard__subscription">
        <div class="onboard__subscription__content">
            <img src="/images/logos/heba-logo--muted.png" alt="Heba Pilates logo" />

            <p class="onboard__subscription__name">{{ title }}</p>
            <strong class="onboard__subscription__price">£{{ price }}</strong>
            <p v-if="frequency" class="onboard__subscription__frequency">/{{ frequency }}</p>

            <div class="onboard__subscription__description">
                <slot></slot>
            </div>
        </div>

        <div class="onboard__subscription__footer">
            <button class="button button--full" @click="$emit('selected')">{{ buttonText }}</button>
        </div>

    </div>
</template>

<script>
export default {
    props: {
        title: String,
        price: String,
        frequency: String,
        buttonText: String
    }
}
</script>