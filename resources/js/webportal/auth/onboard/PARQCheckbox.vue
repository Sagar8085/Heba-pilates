<template>
    <div class="onboard__form__input">
        <form-checkbox-options
            v-model="localValue"
            :label="label"
            :name="name"
            :options="yesNoOptions" />

        <form-input
            v-if="localValue == 'yes'" 
            v-model="localDetails"
            label="Please provide more details" 
            type="textarea" />
    </div>
</template>

<script>
import FormCheckboxOptions from '../../../components/FormCheckboxOptions.vue';
import FormInput from '../../../components/FormInput.vue';

export default {
    components: { FormCheckboxOptions, FormInput },
    props: {
        value: String,
        label: String,
        name: String,
        details: String
    },
    computed: {
        localValue: {
            get () { return this.value },
            set (value) { this.$emit('input', value) }
        },
        localDetails: {
            get () { return this.details },
            set (value) { this.$emit('update:details', value) }
        },
        yesNoOptions () {
            return [
                { value: 'yes', label: 'Yes'},
                { value: 'no', label: 'No'}
            ]
        }
    }
}
</script>