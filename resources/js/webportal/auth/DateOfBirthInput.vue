<template>
    <div class="form-input-group date-of-birth">
        <label v-if="label" class="auth__label">{{ label }}</label>
        <span v-if="dayError" class="auth__label--error">{{ dayError }}</span>
        <span v-if="monthError" class="auth__label--error">{{ monthError }}</span>
        <span v-if="yearError" class="auth__label--error">{{ yearError }}</span>
        <div class="form-row form-row--no-padding form-row--fixed">
            <form-input :value="localDay" @input="updateDay" placeholder="Day" type="number" :min="1" :max="31" :maxLength="2" hideNumberArrows required />
            <form-input :value="localMonth" @input="updateMonth" placeholder="Month" type="select" class="form-input--large no-m-top" required>
                <option v-for="month in months" :key="month.value" :value="month.value">{{ month.label }}</option>
            </form-input>
            <form-input :value="localYear" @input="updateYear" placeholder="Year" type="number" :maxLength="4" class="no-m-top" hideNumberArrows required />
        </div>
        <span v-if="error" class="auth__label--error">{{ error }}</span>
    </div>
</template>

<script>
import moment from 'moment';
import FormInput from '../../components/FormInput.vue';

export default {
    components: { FormInput },
    model: {
        prop: 'date',
        event: 'change'
    },
    props: {
        date: String,
        day: Number,
        month: {
            type: [Number,String],
            default: ''
        },
        year: Number,
        error: String,
        dayError: String,
        monthError: String,
        yearError: String,
        label: String
    },
    data () {
        return {
            localDay: this.day,
            localMonth: this.month,
            localYear: this.year,
        }
    },
    computed: {
        localDate: {
            get () { return this.date },
            set (value) { this.$emit('change', value) }
        },
        months () {
            const months = moment.localeData('gb').months();

            return months.map((month, index) => {
                return {
                    label: month,
                    value: index + 1
                }
            });
        }
    },
    methods: {
        formatDate (date) {
            return moment(date).format('YYYY-MM-DD');
        },
        updateDate () {
            this.localDate = this.formatDate(`${this.localYear}-${this.localMonth}-${this.localDay}`)
        },
        updateDay (value) {
            this.localDay = value;
            this.$emit('update:day', value);
            this.updateDate();
        },
        updateMonth (value) {
            this.localMonth = value;
            this.$emit('update:month', value);
            this.updateDate();
        },
        updateYear (value) {
            this.localYear = value;
            this.$emit('update:year', value);
            this.updateDate();
        }
    },
    mounted () {
        if (!this.date) return;
        // split date into day/month/year locals
        const date = moment(this.localDate);

        this.localDay = date.get('date');
        this.localMonth = date.get('month') + 1;
        this.localYear = date.get('year');
    }
}
</script>
