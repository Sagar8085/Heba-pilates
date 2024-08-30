<template>
    <label class="switch" >
        <div v-if="label" class="toggle-item__increment__label">{{ label }}</div>
        <input v-model="localValue" type="checkbox">
        <span class="slider round"></span>
    </label>
</template>

<script>
export default {
    model: { event: 'change' },
    props: { value: Number, label: String },
    computed: {
        localValue: {
            get () { return this.value },
            set (value) { this.$emit('change', value) }
        }
    }
}
</script>
