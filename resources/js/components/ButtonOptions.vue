<template>
    <div :class="{ 'button-options': true, [`button-options--cols button-options--cols--${columns}`]: columns > 1, 'button-options--center': align == 'center' }">
        <div class="button-options__container">
            <div
                v-for="(option, index) in options"
                :key="index"
                :class="{ 'button-options__option': true, 'button-options__option--selected': multiple ? selected.includes(option.value) : selected == option.value }">
                <slot name="before" :option="option"></slot>
                <label
                    :for="'option-' + option.value"
                    class="button button--outline">
                    {{ option.label }}
                    <input
                        :type="multiple ? 'checkbox' : 'radio'"
                        v-model="selected"
                        :name="name"
                        :id="'option-' + option.value"
                        :value="option.value"
                        @change="handleChange(option.value)" >
                </label>
                <slot name="after" :option="option"></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        // pass options as an array of { label, value } objects
        options: Array,
        value: [String, Array, Number],
        name: {
            type: String,
            default: 'radioOptions'
        },
        columns: {
            type: Number,
            default: 1
        },
        multiple: Boolean,
        align: {
            type: String,
            default: 'left'
        }
    },
    data () {
        return {
            selected: ''
        }
    },
    methods: {
        handleChange (e) {
            this.$emit('input', this.multiple ? this.selected : e);
        }
    },
    created () {
        this.selected = this.value ? this.value : this.multiple ? [] : '';
    }
}
</script>
