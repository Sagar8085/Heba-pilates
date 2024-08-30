<template>
    <label class="slider-input">
        <ul class="slider-input__labels">
            <li 
                v-for="label in labels" 
                :key="label" 
                @click="localValue = label">
                {{ label }}
            </li>
        </ul>

        <input 
            class="slider-input__input" 
            v-model="localValue" 
            type="range" 
            :min="min" 
            :max="max" >
    </label>
</template>

<script>
export default {
    model: { event: 'change' },
    props: { value: Number, label: String, min: Number, max: Number },
    computed: {
        localValue: {
            get () { return this.value },
            set (value) { this.$emit('change', parseInt(value)) }
        },
        labels () {
            const labels = [];

            for (let i = 0; i < this.max; i++) {
                labels.push(this.min + i);
            }

            return labels;
        }
    }
}
</script>
