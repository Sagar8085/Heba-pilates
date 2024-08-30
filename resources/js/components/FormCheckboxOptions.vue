<template>
    <div class="checkbox-options">
        <p v-if="label" class="checkbox-options__label">{{ label }}</p>

        <div v-for="option in options" :key="option.value" :class="formInputClasses(option)">
            <label :for="inputId(option)" class="form-input__container">
                <i class="material-icons">{{ localInput == option.value ? 'check_box' : 'check_box_outline_blank' }}</i>
                <div class="auth__label">{{ option.label }}</div>
                <input 
                    class="form-input__input"
                    type="radio"
                    :name="name"
                    :id="inputId(option)"
                    :value="option.value"
                    v-model="localInput" />
            </label>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        value: [String,Boolean],
        label: String,
        name: String,
        options: Array // pass options as an array of { label, value } objects
    },
    computed: {
        localInput: {
            get () { return this.value },
            set (value) { this.$emit('input', value) }
        }
    },
    methods: {
        inputId (option) {
            return this.name + '-input-' + option.value;
        },
        formInputClasses (option) {
            return { 
                'form-input form-input--checkbox': true, 
                'form-input--filled': this.localInput == option.value
            }
        }
    }
}
</script>