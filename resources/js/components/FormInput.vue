<template>
    <div :class="formInputClasses">
        
        <label v-if="type == 'checkbox'" :for="inputId" class="form-input__container">
            <i class="material-icons">{{ localInput ? 'check_box' : 'check_box_outline_blank' }}</i>
            <slot name="label"><div class="auth__label">{{ label }}</div></slot>
            <input 
                class="form-input__input"
                :class="{ 'form-input__error': error, 'form-input': !error }"
                type="checkbox"
                :id="inputId"
                v-model="localInput" />
        </label>
        
        <label v-else :for="inputId" class="form-input__container">
            <slot name="label">
                <div
                    v-if="label"
                    class="auth__label"
                    :class="{ 'auth__label__error': error, 'auth__label': !error }"
                >
                    {{ label }}
                </div>
            </slot>
            <div class="auth__input">
                <span v-if="before" class="form-input__before">{{ before }}</span>
                <slot name="before"></slot>

                <template v-if="type == 'select'">
                    <select 
                        v-model="localInput" 
                        ref="input" 
                        class="form-input__input"
                        :class="{ 'form-input__error': error, 'form-input__input': !error }"
                        :id="inputId"
                        :required="required"
                        @focus="onFocus"
                        @blur="onBlur">
                        <option v-if="placeholder" value="" disabled="disabled" selected="selected" hidden="hidden">{{ placeholder }}</option>
                        <slot>
                            <!-- options -->
                        </slot>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </template>

                <template v-else-if="type == 'calendar'">
                    <div class="form-input__input" ref="input" @click="showCalendar = !showCalendar">
                        {{ localInput ? formatDate(localInput) : placeholder }}
                    </div>
                    <i class="fas fa-chevron-down"></i>
                    <DatePicker
                        v-if="showCalendar"
                        v-on-clickaway="closeCalendar"
                        v-model="localInput"
                        color="red"
                        is-dark
                        @input="closeCalendar" />
                </template>

                <template v-else-if="type == 'textarea'">
                    <textarea
                        ref="input"
                        class="form-input__input"
                        :id="inputId" 
                        v-model="localInput" 
                        :maxlength="maxLength" 
                        :placeholder="placeholder"
                        :required="required"
                        :rows="rows"
                        @focus="onFocus"
                        @blur="onBlur">
                    </textarea>
                </template>

                <slot v-else :on-focus="onFocus" :on-blur="onBlur">
                    <input 
                        ref="input"
                        class="form-input__input"
                        :class="{ 'form-input__error': error, 'form-input__input': !error }"
                        :id="inputId"
                        v-model="localInput" 
                        :type="inputType" 
                        :maxlength="maxLength" 
                        :min="min"
                        :max="max" 
                        :placeholder="placeholder"
                        :required="required"
                        :autocomplete="autocomplete"
                        @animationstart="checkForAutofill"
                        @focus="onFocus"
                        @blur="onBlur"
                        @keyup.enter="$emit('keyup', { type: 'enter' })"
                        @keypress="onKeypress" />
                </slot>
                <slot name="after-input"></slot>
            </div>    
            <slot name="after"></slot>
        </label>
        <span v-if="error" class="auth__label--error">{{ error }}</span>
    </div>
</template>

<script>
import DatePicker from 'v-calendar/lib/components/date-picker.umd'
import moment from 'moment'
import { mixin as clickaway } from 'vue-clickaway';

export default {
    components: { DatePicker },
    mixins: [ clickaway ],
    props: {
        value: [String,Number,Boolean,Date],
        label: String,
        type: {
            default: 'text'
        },
        maxLength: Number,
        hideNumberArrows: Boolean,
        before: String,
        allowStartingZero: Boolean,
        min: [String,Number],
        max: [String,Number],
        staticLabel: {
            default: true,
            type: Boolean
        },
        placeholder: String,
        required: Boolean,
        rounded: Boolean,
        error: String,
        autocomplete: [String,Boolean],
        rows: {
            type: Number,
            default: 5
        }
    },
    data () {
        return {
            isFocused: false,
            autofilled: false,
            showCalendar: false
        }
    },
    computed: {
        localInput: {
            get () {
                return this.value
            },
            set (value) {
                this.$emit('input', this.type == 'number' && !this.allowStartingZero ? parseInt(value) : value)
            }
        },
        inputId () {
            return this.label ? 'input-' + this.label : null;
        },
        inputType () {
            return this.type == 'number' && this.allowStartingZero ? 'text' : this.type;
        },
        isFilled () {
            return this.value || this.autofilled;
        },
        formInputClasses () {
            return { 
                'form-input': true, 
                'form-input--plain': this.hideNumberArrows, 
                'form-input--focused': this.isFocused, 
                'form-input--filled': this.isFilled, 
                'form-input--static': this.staticLabel || this.placeholder || this.before || this.type == 'date',
                'form-input--checkbox': this.type == 'checkbox',
                'form-input--no-label': !this.label,
                'form-input--rounded': this.rounded,
                'form-input--calendar': this.type == 'calendar',
                'form-input--textarea': this.type == 'textarea'
            }
        }
    },
    methods: {
        closeCalendar () {
            this.showCalendar = false;
        },
        onKeypress (e) {
            if (
                (this.maxLength && e.target.value.length >= this.maxLength)
                || (this.type == 'number' && isNaN(parseInt(e.key)))) 
                return e.preventDefault();
        },
        focus () {
            this.$refs.input.focus()
        },
        onFocus () {
            this.isFocused = true;
        },
        onBlur () {
            this.isFocused = false;
        },
        checkForAutofill (e) {
            this.autofilled = e.animationName == 'onAutoFillStart';
        },
        formatDate (value) {
            return moment(value).format('Do MMM YYYY')
        }
    }
}
</script>

<style lang="scss">
.form-input input {
    &:-webkit-autofill {
        animation-name: onAutoFillStart;
    }
    &:not(:-webkit-autofill) {
        animation-name: onAutoFillCancel;
    }

    @keyframes onAutoFillStart { from { } to { } }
    @keyframes onAutoFillCancel { from { } to { } }
}
</style>
