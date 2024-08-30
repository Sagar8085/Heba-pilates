<template>
<div class="form-element">
    <span class="form-element__label">
        <template v-if="required">*</template>
        {{ label }}
        <span v-if="error">{{ error }}</span>
    </span>

    <div v-if="type == 'select'" class="form-element__control form-element__control--select">
        <select v-model="localValue">
            <option v-if="placeholder" value="" disabled="disabled" selected="selected" hidden="hidden">{{ placeholder }}</option>
            <slot>
                <!-- options -->
            </slot>
        </select>
    </div>

    <div v-else-if="type == 'file'" class="form-element__control form-element__control--file">
        <slot v-if="imagePreview.length > 0 && !hideImagePreview" name="preview" :preview="allowMultiple ? imagePreview : imagePreview[0]">
            <div class="form-element__preview">
                <img v-for="(src, i) in imagePreview" :key="i" :src="src" alt="Image Preview" />
            </div>
        </slot>
        <slot :open="openFileBrowser" :preview="allowMultiple ? imagePreview : imagePreview[0]">
            <div class="chat__files" @click="openFileBrowser">
                <span v-if="!localValue.name">Click to Upload File <img src="/icons/utility/attach_60.png"></span>
                <span v-else>{{localValue.name}}</span>
            </div>
        </slot>
        <input type="file" ref="image" :multiple="allowMultiple" @change="onFileUpload" :accept="acceptedFiles">
    </div>

    <div v-else-if="type == 'textarea'" class="form-element__control">
        <slot>
            <textarea v-model="localValue" :required="required" :placeholder="placeholder" :rows="rows" :maxlength="maxlength" />
        </slot>
    </div>

    <div v-else-if="type == 'calendar'" :class="`form-element__control form-element__control--calendar form-element__control--calendar-${calendarPosition}`">
        <div class="form-element__control__input" @click="showCalendar = !showCalendar">
            {{ localValue ? formatDate(localValue) : placeholder }}

            <button v-if="localValue" class="button button--icon button--transparent" title="Remove" @click="localValue = ''">
                <i class="material-icons">close</i>
            </button>
        </div>
        <DatePicker
            v-if="showCalendar"
            v-on-clickaway="closeCalendar"
            v-model="localValue"
            :min-date="minDate"
            :max-date="maxDate"
            color="blue"
            @input="closeCalendar" />
    </div>

    <div v-else class="form-element__control">
        <slot>
            <input v-model.trim="localValue" :type="type" :required="required" :placeholder="placeholder">
        </slot>
    </div>
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
        value: [String, Number, Object, Array, File, Date],
        label: String,
        type: {
            default: 'text',
            type: String
        },
        required: Boolean,
        error: String,
        placeholder: String,
        // for File type inputs
        accept: {
            type: String, // image || video
            default: 'image'
        },
        allowMultiple: Boolean,
        hideImagePreview: Boolean,
        // for Textarea inputs
        rows: {
            type: Number,
            default: 5
        },
        maxlength: Number,
        // for Calendar inputs
        calendarPosition: {
            type: String,
            default: 'left' // left || right
        },
        minDate: [Date, String],
        maxDate: [Date, String]
    },
    data () {
        return {
            imagePreview: [],
            showCalendar: false
        }
    },
    computed: {
        localValue: {
            get () { return this.value },
            set (value) { return this.$emit('input', value) }
        },
        acceptedFiles () {
            if (this.accept == 'image')
                return 'image/jpg,image/jpeg,image/png,image/webp';

            if (this.accept == 'video')
                return 'video/mp4';

            return this.accept;
        }
    },
    methods: {
        closeCalendar () {
            this.showCalendar = false;
        },
        formatDate (value) {
            return moment(value).format('YYYY/MM/DD')
        },
        openFileBrowser () {
            this.$refs.image.click()
        },
        onFileUpload (e) {
            if (e.target.files.length == 0) return;

            this.localValue = this.allowMultiple ?
                e.target.files : e.target.files[0];

            // Generate image preview
            const reader = new FileReader();
            this.imagePreview = [];

            reader.onload = e => {
                this.imagePreview.push(e.target.result);
            }

            for (const file of e.target.files) {
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>
