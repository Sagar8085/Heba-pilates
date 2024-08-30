<template>
    <label class="toggle-switch">
        <span v-if="showLabel" class="toggle-switch__label">{{ label }}</span>

        <div class="toggle-switch__input">
            <input v-model="localValue" type="checkbox" />
            <span class="toggle-switch__toggle"></span>
        </div>
    </label>
</template>

<script>
export default {
    props: { value: Boolean, label: String, showLabel: Boolean },

    computed: {
        localValue: {
            get () { return this.value },
            set (value) { this.$emit('input', value) }
        }
    }
}
</script>