<template>
    <div class="graph graph--plain">
        <header class="graph__header">
            <div class="graph__header__content">
                <p v-if="title" :class="{ 'graph__header__title': true, 'graph__header__title--small': !largeTitle }">
                    {{ title }}
                    <i v-if="showInfo" class="graph__header__info material-icons" @click="openInfo">info</i>
                </p>
                <span v-if="subtitle" class="graph__header__subtitle">
                    {{ subtitle }}
                </span>
            </div>
        </header>

        <section class="graph__values">
            <div v-if="graphValues && graphValues[0]" class="graph__values__container">
                <span class="graph__value graph__value--small graph__value--primary">{{ graphValues[0].value }}</span>
                <span v-if="graphValues[1]" class="graph__value graph__value--small graph__value--grey">{{ graphValues[1].value }}</span>
                <span v-if="graphValues[0].valueDiffPercentage" :class="valueDiffClasses">{{ graphValues[0].valueDiffPercentage }}</span>
            </div>
            <slot name="toggle"></slot>
        </section>

        <section class="graph__container">
            <slot></slot>
        </section>
    </div>
</template>

<script>
export default {
    props: {
        showInfo: Boolean,
        largeTitle: Boolean,
        title: String,
        subtitle: String,
        graphValues: Array // { value: String, valueDiffPercentage?: String, valueDiffType?: String }[]
    },

    computed: {
        valueDiffClasses () {
            return {
                'graph__value-diff': true,
                [`graph__value-diff--${this.graphValues[0].valueDiffType}`]: this.graphValues && this.graphValues[0] && this.graphValues[0].valueDiffType
            }
        }
    },

    methods: {
        openInfo () {
            this.$emit('info');
        }
    }
}
</script>